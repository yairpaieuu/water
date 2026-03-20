<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\ServiceContract;
use App\Models\ServiceJob;
use App\Models\ServiceType;
use App\Models\User;

class ServiceController extends BaseController
{
    public function indexJobs(): void
    {
        $request  = new Request();
        $status   = $request->get('status', '');
        $branchId = (int) $request->get('branch_id', 0);
        $date     = $request->get('date', '');

        $db     = Database::getInstance();
        $where  = [];
        $params = [];

        if ($status !== '') {
            $where[]  = 'sj.`status` = ?';
            $params[] = $status;
        }
        if ($branchId > 0) {
            $where[]  = 'sj.`branch_id` = ?';
            $params[] = $branchId;
        }
        if ($date !== '') {
            $where[]  = 'DATE(sj.`scheduled_date`) = ?';
            $params[] = $date;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $jobs     = $db->fetchAll(
            "SELECT sj.*,
                    CONCAT(c.`first_name`, ' ', c.`last_name`) AS customer_name,
                    c.`phone` AS customer_phone,
                    b.`name`  AS branch_name,
                    u.`name`  AS assigned_to_name
               FROM `service_jobs` sj
               JOIN `customers` c ON c.`id` = sj.`customer_id`
          LEFT JOIN `branches`  b ON b.`id` = sj.`branch_id`
          LEFT JOIN `users`     u ON u.`id` = sj.`assigned_to`"
            . $whereSql
            . " ORDER BY sj.`scheduled_date` DESC",
            $params
        );

        $this->render('services.jobs', [
            'pageTitle' => 'Service Jobs',
            'jobs'      => $jobs,
            'branches'  => (new Branch())->getActive(),
            'status'    => $status,
            'branchId'  => $branchId,
            'date'      => $date,
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function createJob(): void
    {
        $this->render('services.create_job', [
            'pageTitle'    => 'New Service Job',
            'customers'    => Database::getInstance()->fetchAll(
                "SELECT id, customer_code, first_name, last_name, phone
                   FROM `customers` WHERE `status` = 'active'
                ORDER BY `first_name` ASC, `last_name` ASC"
            ),
            'employees'    => (new Employee())->getTechnicians(),
            'branches'     => (new Branch())->getActive(),
            'serviceTypes' => (new ServiceType())->findAll([], 'name ASC'),
            'contracts'    => [],
            'csrfField'    => CSRF::field(),
            'user'         => Auth::user(),
            'error'        => Session::getFlash('error'),
        ]);
    }

    public function storeJob(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $data = [
            'customer_id'    => (int) $request->post('customer_id', 0),
            'contract_id'    => (int) $request->post('contract_id', 0),
            'branch_id'      => (int) $request->post('branch_id', 0),
            'assigned_to'    => (int) $request->post('assigned_to', 0),
            'job_type'       => $request->post('job_type', 'maintenance'),
            'scheduled_date' => $request->post('scheduled_date', ''),
            'scheduled_time' => $request->post('scheduled_time', ''),
            'priority'       => $request->post('priority', 'normal'),
            'notes'          => $request->post('notes', ''),
            'cost'           => (float) $request->post('cost', 0),
            'status'         => 'pending',
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'customer_id'    => 'required|numeric',
            'scheduled_date' => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if ($data['contract_id'] === 0) {
            unset($data['contract_id']);
        }
        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }
        if ($data['assigned_to'] === 0) {
            unset($data['assigned_to']);
        }

        $jobModel         = new ServiceJob();
        $data['job_code'] = $jobModel->generateCode();
        $data['created_by'] = Auth::id();

        $id = $jobModel->create($data);
        if ($id === false) {
            $this->error('Failed to create service job. Please try again.');
        }

        $this->success('Service job created successfully.', "/jobs/{$id}");
    }

    public function showJob(string $id): void
    {
        $jobId = (int) $id;
        $job   = (new ServiceJob())->getWithDetails($jobId);

        if ($job === false) {
            $this->error('Service job not found.', '/jobs');
        }

        $this->render('services.show_job', [
            'pageTitle' => $job['job_code'],
            'job'       => $job,
            'employees' => (new Employee())->getTechnicians(),
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function updateJob(string $id): void
    {
        $jobId   = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $job = (new ServiceJob())->find($jobId);
        if ($job === false) {
            $this->error('Service job not found.', '/jobs');
        }

        $allowedStatuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $status          = $request->post('status', $job['status']);

        if (!in_array($status, $allowedStatuses, true)) {
            $this->error('Invalid job status.', "/jobs/{$jobId}");
        }

        $data = [
            'status'           => $status,
            'assigned_to'      => (int) $request->post('assigned_to', 0),
            'scheduled_date'   => $request->post('scheduled_date', $job['scheduled_date']),
            'notes'            => $request->post('notes', $job['notes'] ?? ''),
            'completion_notes' => $request->post('completion_notes', $job['completion_notes'] ?? ''),
        ];

        if ($data['assigned_to'] === 0) {
            unset($data['assigned_to']);
        }

        (new ServiceJob())->update($jobId, $data);
        $this->success('Service job updated successfully.', "/jobs/{$jobId}");
    }

    public function indexContracts(): void
    {
        $request  = new Request();
        $status   = $request->get('status', '');
        $branchId = (int) $request->get('branch_id', 0);

        $db     = Database::getInstance();
        $where  = [];
        $params = [];

        if ($status !== '') {
            $where[]  = 'sc.`status` = ?';
            $params[] = $status;
        }
        if ($branchId > 0) {
            $where[]  = 'sc.`branch_id` = ?';
            $params[] = $branchId;
        }

        $whereSql  = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $contracts = $db->fetchAll(
            "SELECT sc.*,
                    CONCAT(c.`first_name`, ' ', c.`last_name`) AS customer_name,
                    c.`phone` AS customer_phone,
                    p.`name`  AS product_name,
                    b.`name`  AS branch_name
               FROM `service_contracts` sc
               JOIN `customers` c ON c.`id` = sc.`customer_id`
          LEFT JOIN `products`  p ON p.`id` = sc.`product_id`
          LEFT JOIN `branches`  b ON b.`id` = sc.`branch_id`"
            . $whereSql
            . " ORDER BY sc.`created_at` DESC",
            $params
        );

        $this->render('services.contracts', [
            'pageTitle' => 'Service Contracts',
            'contracts' => $contracts,
            'branches'  => (new Branch())->getActive(),
            'status'    => $status,
            'branchId'  => $branchId,
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function createContract(): void
    {
        $this->render('services.create_contract', [
            'pageTitle'    => 'New Service Contract',
            'customers'    => Database::getInstance()->fetchAll(
                "SELECT id, customer_code, first_name, last_name
                   FROM `customers` WHERE `status` = 'active'
                ORDER BY `first_name` ASC, `last_name` ASC"
            ),
            'products'     => Database::getInstance()->fetchAll(
                "SELECT id, product_code, name FROM `products`
                  WHERE `status` = 'active' ORDER BY `name` ASC"
            ),
            'serviceTypes' => (new ServiceType())->findAll([], 'name ASC'),
            'branches'     => (new Branch())->getActive(),
            'csrfField'    => CSRF::field(),
            'user'         => Auth::user(),
            'error'        => Session::getFlash('error'),
        ]);
    }

    public function storeContract(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $data = [
            'customer_id'       => (int) $request->post('customer_id', 0),
            'product_id'        => (int) $request->post('product_id', 0),
            'service_type_id'   => (int) $request->post('service_type_id', 0),
            'branch_id'         => (int) $request->post('branch_id', 0),
            'installation_date' => $request->post('installation_date', ''),
            'next_service_date' => $request->post('next_service_date', ''),
            'serial_number'     => $request->post('serial_number', ''),
            'location_notes'    => $request->post('location_notes', ''),
            'status'            => 'active',
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'customer_id' => 'required|numeric',
            'start_date'  => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        foreach (['product_id', 'service_type_id', 'branch_id'] as $field) {
            if ($data[$field] === 0) {
                $data[$field] = null;
            }
        }

        $contractModel            = new ServiceContract();
        $data['contract_code']    = $contractModel->generateCode();
        $data['created_by']       = Auth::id();

        $id = $contractModel->create($data);
        if ($id === false) {
            $this->error('Failed to create contract. Please try again.');
        }

        $this->success('Service contract created successfully.', "/contracts/{$id}");
    }

    public function showContract(string $id): void
    {
        $contractId = (int) $id;
        $contract   = (new ServiceContract())->getWithDetails($contractId);

        if ($contract === false) {
            $this->error('Contract not found.', '/contracts');
        }

        $jobs = Database::getInstance()->fetchAll(
            "SELECT * FROM `service_jobs` WHERE `contract_id` = ?
             ORDER BY `scheduled_date` DESC",
            [$contractId]
        );

        $this->render('services.show_contract', [
            'pageTitle' => $contract['contract_code'],
            'contract'  => $contract,
            'jobs'      => $jobs,
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }
}
