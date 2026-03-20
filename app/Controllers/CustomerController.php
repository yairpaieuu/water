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
use App\Models\ServiceContract;

class CustomerController extends BaseController
{
    private const PAGE_SIZE = 20;

    public function index(): void
    {
        $request  = new Request();
        $q        = $request->get('q', '');
        $branchId = (int) $request->get('branch_id', 0);
        $page     = max(1, (int) $request->get('page', 1));
        $offset   = ($page - 1) * self::PAGE_SIZE;

        $customerModel = new Customer();
        $db            = Database::getInstance();

        if ($q !== '') {
            $all       = $customerModel->search($q, $branchId > 0 ? $branchId : null);
            $total     = count($all);
            $customers = array_slice($all, $offset, self::PAGE_SIZE);
        } else {
            $where  = $branchId > 0 ? ' WHERE c.`branch_id` = ?' : '';
            $params = $branchId > 0 ? [$branchId] : [];

            $countRow = $db->fetch(
                "SELECT COUNT(*) AS cnt FROM `customers` c" . $where,
                $params
            );
            $total = (int) ($countRow['cnt'] ?? 0);

            $listParams   = $params;
            $listParams[] = self::PAGE_SIZE;
            $listParams[] = $offset;
            $customers    = $db->fetchAll(
                "SELECT c.*, b.`name` AS branch_name
                   FROM `customers` c
              LEFT JOIN `branches` b ON b.`id` = c.`branch_id`"
                . $where
                . " ORDER BY c.`created_at` DESC LIMIT ? OFFSET ?",
                $listParams
            );
        }

        $this->render('customers.index', [
            'pageTitle' => 'Customers',
            'customers' => $customers,
            'branches'  => (new Branch())->getActive(),
            'q'         => $q,
            'branchId'  => $branchId,
            'page'      => $page,
            'total'     => $total,
            'pageSize'  => self::PAGE_SIZE,
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $this->render('customers.create', [
            'pageTitle' => 'Add Customer',
            'branches'  => (new Branch())->getActive(),
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $data = [
            'first_name' => $request->post('first_name', ''),
            'last_name'  => $request->post('last_name', ''),
            'email'      => $request->post('email', ''),
            'phone'      => $request->post('phone', ''),
            'address'    => $request->post('address', ''),
            'city'       => $request->post('city', ''),
            'branch_id'  => (int) $request->post('branch_id', 0),
            'notes'      => $request->post('notes', ''),
            'status'     => 'active',
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'first_name' => 'required|max:100',
            'email'      => 'email',
            'phone'      => 'required|max:30',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }

        $customerModel         = new Customer();
        $data['customer_code'] = $customerModel->generateCode();

        $id = $customerModel->create($data);
        if ($id === false) {
            $this->error('Failed to create customer. Please try again.');
        }

        $this->success('Customer created successfully.', "/customers/{$id}");
    }

    public function show(string $id): void
    {
        $customerId = (int) $id;
        $customer   = (new Customer())->getWithBranch($customerId);

        if ($customer === false) {
            $this->error('Customer not found.', '/customers');
        }

        $contracts  = (new ServiceContract())->getByCustomer($customerId);
        $recentJobs = Database::getInstance()->fetchAll(
            "SELECT sj.*, b.`name` AS branch_name, u.`name` AS assigned_to_name
               FROM `service_jobs` sj
          LEFT JOIN `branches` b ON b.`id` = sj.`branch_id`
          LEFT JOIN `users`    u ON u.`id` = sj.`assigned_to`
              WHERE sj.`customer_id` = ?
           ORDER BY sj.`created_at` DESC
              LIMIT 10",
            [$customerId]
        );

        $this->render('customers.show', [
            'pageTitle'  => $customer['first_name'] . ' ' . $customer['last_name'],
            'customer'   => $customer,
            'contracts'  => $contracts,
            'recentJobs' => $recentJobs,
            'user'       => Auth::user(),
            'success'    => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function edit(string $id): void
    {
        $customerId = (int) $id;
        $customer   = (new Customer())->find($customerId);

        if ($customer === false) {
            $this->error('Customer not found.', '/customers');
        }

        $this->render('customers.edit', [
            'pageTitle' => 'Edit Customer',
            'customer'  => $customer,
            'branches'  => (new Branch())->getActive(),
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $customerId = (int) $id;
        $request    = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $customer = (new Customer())->find($customerId);
        if ($customer === false) {
            $this->error('Customer not found.', '/customers');
        }

        $data = [
            'first_name' => $request->post('first_name', ''),
            'last_name'  => $request->post('last_name', ''),
            'email'      => $request->post('email', ''),
            'phone'      => $request->post('phone', ''),
            'address'    => $request->post('address', ''),
            'city'       => $request->post('city', ''),
            'branch_id'  => (int) $request->post('branch_id', 0),
            'status'     => $request->post('status', 'active'),
            'notes'      => $request->post('notes', ''),
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'first_name' => 'required|max:100',
            'email'      => 'email',
            'phone'      => 'required|max:30',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }

        (new Customer())->update($customerId, $data);
        $this->success('Customer updated successfully.', "/customers/{$customerId}");
    }

    public function delete(string $id): void
    {
        $customerId = (int) $id;
        $request    = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $customer = (new Customer())->find($customerId);
        if ($customer === false) {
            $this->error('Customer not found.', '/customers');
        }

        (new Customer())->delete($customerId);
        $this->success('Customer deleted successfully.', '/customers');
    }
}
