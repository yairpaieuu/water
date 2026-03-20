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
use App\Models\Lead;
use App\Models\User;

class LeadController extends BaseController
{
    public function index(): void
    {
        $request  = new Request();
        $status   = $request->get('status', '');
        $branchId = (int) $request->get('branch_id', 0);

        $db     = Database::getInstance();
        $where  = [];
        $params = [];

        if ($status !== '') {
            $where[]  = 'l.`status` = ?';
            $params[] = $status;
        }
        if ($branchId > 0) {
            $where[]  = 'l.`branch_id` = ?';
            $params[] = $branchId;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $leads    = $db->fetchAll(
            "SELECT l.*, b.`name` AS branch_name, u.`name` AS assigned_to_name
               FROM `leads` l
          LEFT JOIN `branches` b ON b.`id` = l.`branch_id`
          LEFT JOIN `users`    u ON u.`id` = l.`assigned_to`"
            . $whereSql
            . " ORDER BY l.`created_at` DESC",
            $params
        );

        $this->render('leads.index', [
            'pageTitle' => 'Leads',
            'leads'     => $leads,
            'branches'  => (new Branch())->getActive(),
            'status'    => $status,
            'branchId'  => $branchId,
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $this->render('leads.create', [
            'pageTitle' => 'Add Lead',
            'branches'  => (new Branch())->getActive(),
            'users'     => (new User())->findAll(['status' => 'active'], 'name ASC'),
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
            'first_name'  => $request->post('first_name', ''),
            'last_name'   => $request->post('last_name', ''),
            'email'       => $request->post('email', ''),
            'phone'       => $request->post('phone', ''),
            'address'     => $request->post('address', ''),
            'city'        => $request->post('city', ''),
            'branch_id'   => (int) $request->post('branch_id', 0),
            'source'      => $request->post('source', ''),
            'assigned_to' => (int) $request->post('assigned_to', 0),
            'notes'       => $request->post('notes', ''),
            'status'      => 'new',
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'first_name' => 'required|max:100',
            'phone'      => 'required|max:30',
            'email'      => 'email',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }
        if ($data['assigned_to'] === 0) {
            unset($data['assigned_to']);
        }

        $leadModel         = new Lead();
        $data['lead_code'] = $leadModel->generateCode();
        $data['created_by'] = Auth::id();

        $id = $leadModel->create($data);
        if ($id === false) {
            $this->error('Failed to create lead. Please try again.');
        }

        $this->success('Lead created successfully.', "/leads/{$id}");
    }

    public function show(string $id): void
    {
        $leadId = (int) $id;
        $lead   = (new Lead())->getWithDetails($leadId);

        if ($lead === false) {
            $this->error('Lead not found.', '/leads');
        }

        $this->render('leads.show', [
            'pageTitle' => $lead['first_name'] . ' ' . $lead['last_name'],
            'lead'      => $lead,
            'users'     => (new User())->findAll(['status' => 'active'], 'name ASC'),
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $leadId  = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $lead = (new Lead())->find($leadId);
        if ($lead === false) {
            $this->error('Lead not found.', '/leads');
        }

        $data = [
            'first_name'  => $request->post('first_name', ''),
            'last_name'   => $request->post('last_name', ''),
            'email'       => $request->post('email', ''),
            'phone'       => $request->post('phone', ''),
            'address'     => $request->post('address', ''),
            'city'        => $request->post('city', ''),
            'branch_id'   => (int) $request->post('branch_id', 0),
            'source'      => $request->post('source', ''),
            'assigned_to' => (int) $request->post('assigned_to', 0),
            'status'      => $request->post('status', 'new'),
            'notes'       => $request->post('notes', ''),
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'first_name' => 'required|max:100',
            'phone'      => 'required|max:30',
            'email'      => 'email',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }
        if ($data['assigned_to'] === 0) {
            unset($data['assigned_to']);
        }

        (new Lead())->update($leadId, $data);
        $this->success('Lead updated successfully.', "/leads/{$leadId}");
    }

    public function delete(string $id): void
    {
        $leadId  = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $lead = (new Lead())->find($leadId);
        if ($lead === false) {
            $this->error('Lead not found.', '/leads');
        }

        (new Lead())->delete($leadId);
        $this->success('Lead deleted successfully.', '/leads');
    }

    public function convert(string $id): void
    {
        $leadId  = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $lead = (new Lead())->find($leadId);
        if ($lead === false) {
            $this->error('Lead not found.', '/leads');
        }

        if (($lead['status'] ?? '') === 'won') {
            $this->error('This lead has already been converted.', "/leads/{$leadId}");
        }

        $overrides = [];
        $branchId  = (int) $request->post('branch_id', 0);
        if ($branchId > 0) {
            $overrides['branch_id'] = $branchId;
        }

        $customerId = (new Lead())->convertToCustomer($leadId, $overrides);
        if ($customerId === false) {
            $this->error('Failed to convert lead to customer. Please try again.');
        }

        $this->success('Lead converted to customer successfully.', "/customers/{$customerId}");
    }
}
