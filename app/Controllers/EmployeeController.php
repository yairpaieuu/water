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
use App\Models\Employee;

class EmployeeController extends BaseController
{
    public function index(): void
    {
        $request    = new Request();
        $department = $request->get('department', '');
        $branchId   = (int) $request->get('branch_id', 0);
        $status     = $request->get('status', '');

        $db     = Database::getInstance();
        $where  = [];
        $params = [];

        if ($department !== '') {
            $where[]  = 'e.`department` = ?';
            $params[] = $department;
        }
        if ($branchId > 0) {
            $where[]  = 'e.`branch_id` = ?';
            $params[] = $branchId;
        }
        if ($status !== '') {
            $where[]  = 'e.`status` = ?';
            $params[] = $status;
        }

        $whereSql  = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $employees = $db->fetchAll(
            "SELECT e.*, b.`name` AS branch_name
               FROM `employees` e
          LEFT JOIN `branches` b ON b.`id` = e.`branch_id`"
            . $whereSql
            . " ORDER BY e.`first_name` ASC, e.`last_name` ASC",
            $params
        );

        $this->render('employees.index', [
            'pageTitle'  => 'Employees',
            'employees'  => $employees,
            'branches'   => (new Branch())->getActive(),
            'department' => $department,
            'branchId'   => $branchId,
            'status'     => $status,
            'user'       => Auth::user(),
            'success'    => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $this->render('employees.create', [
            'pageTitle' => 'Add Employee',
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
            'first_name'  => $request->post('first_name', ''),
            'last_name'   => $request->post('last_name', ''),
            'email'       => $request->post('email', ''),
            'phone'       => $request->post('phone', ''),
            'department'  => $request->post('department', ''),
            'position'    => $request->post('position', ''),
            'branch_id'   => (int) $request->post('branch_id', 0),
            'date_joined' => $request->post('date_joined', ''),
            'salary'      => (float) $request->post('salary', 0),
            'address'     => $request->post('address', ''),
            'status'      => 'active',
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'first_name' => 'required|max:100',
            'phone'      => 'required|max:30',
            'email'      => 'email',
            'department' => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }

        $employeeModel            = new Employee();
        $data['employee_code']    = $employeeModel->generateCode();

        $id = $employeeModel->create($data);
        if ($id === false) {
            $this->error('Failed to create employee. Please try again.');
        }

        $this->success('Employee created successfully.', "/employees/{$id}");
    }

    public function show(string $id): void
    {
        $employeeId = (int) $id;
        $employee   = (new Employee())->getWithBranch($employeeId);

        if ($employee === false) {
            $this->error('Employee not found.', '/employees');
        }

        $recentAttendance = Database::getInstance()->fetchAll(
            "SELECT * FROM `attendance`
              WHERE `employee_id` = ?
           ORDER BY `date` DESC
              LIMIT 30",
            [$employeeId]
        );

        $this->render('employees.show', [
            'pageTitle'        => $employee['first_name'] . ' ' . $employee['last_name'],
            'employee'         => $employee,
            'recentAttendance' => $recentAttendance,
            'csrfField'        => CSRF::field(),
            'user'             => Auth::user(),
            'success'          => Session::getFlash('success'),
            'error'            => Session::getFlash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $employeeId = (int) $id;
        $request    = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $employee = (new Employee())->find($employeeId);
        if ($employee === false) {
            $this->error('Employee not found.', '/employees');
        }

        $data = [
            'first_name'  => $request->post('first_name', ''),
            'last_name'   => $request->post('last_name', ''),
            'email'       => $request->post('email', ''),
            'phone'       => $request->post('phone', ''),
            'department'  => $request->post('department', ''),
            'position'    => $request->post('position', ''),
            'branch_id'   => (int) $request->post('branch_id', 0),
            'date_joined' => $request->post('date_joined', ''),
            'salary'      => (float) $request->post('salary', 0),
            'address'     => $request->post('address', ''),
            'status'      => $request->post('status', 'active'),
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'first_name' => 'required|max:100',
            'phone'      => 'required|max:30',
            'email'      => 'email',
            'department' => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }

        (new Employee())->update($employeeId, $data);
        $this->success('Employee updated successfully.', "/employees/{$employeeId}");
    }

    public function indexAttendance(): void
    {
        $request  = new Request();
        $date     = $request->get('date', date('Y-m-d'));
        $branchId = (int) $request->get('branch_id', 0);

        $db     = Database::getInstance();
        $where  = ['DATE(a.`date`) = ?'];
        $params = [$date];

        if ($branchId > 0) {
            $where[]  = 'e.`branch_id` = ?';
            $params[] = $branchId;
        }

        $whereSql  = ' WHERE ' . implode(' AND ', $where);
        $records   = $db->fetchAll(
            "SELECT a.*,
                    e.`employee_code`,
                    CONCAT(e.`first_name`, ' ', e.`last_name`) AS employee_name,
                    e.`department`,
                    b.`name` AS branch_name
               FROM `attendance` a
               JOIN `employees` e ON e.`id` = a.`employee_id`
          LEFT JOIN `branches`  b ON b.`id` = e.`branch_id`"
            . $whereSql
            . " ORDER BY e.`first_name` ASC",
            $params
        );

        $this->render('employees.attendance', [
            'pageTitle' => 'Attendance',
            'records'   => $records,
            'branches'  => (new Branch())->getActive(),
            'date'      => $date,
            'branchId'  => $branchId,
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function storeAttendance(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $employeeId = (int) $request->post('employee_id', 0);
        $date       = $request->post('date', date('Y-m-d'));
        $status     = $request->post('status', 'present');
        $checkIn    = $request->post('check_in', '');
        $checkOut   = $request->post('check_out', '');
        $notes      = $request->post('notes', '');

        $validator = new Validator();
        $errors    = $validator->validate(
            ['employee_id' => (string) $employeeId, 'date' => $date, 'status' => $status],
            ['employee_id' => 'required|numeric', 'date' => 'required', 'status' => 'required']
        );

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        $db  = Database::getInstance();
        $existing = $db->fetch(
            "SELECT `id` FROM `attendance`
              WHERE `employee_id` = ? AND DATE(`date`) = ? LIMIT 1",
            [$employeeId, $date]
        );

        $attData = [
            'employee_id' => $employeeId,
            'date'        => $date,
            'status'      => $status,
            'check_in'    => $checkIn !== '' ? $checkIn : null,
            'check_out'   => $checkOut !== '' ? $checkOut : null,
            'notes'       => $notes,
        ];

        if ($existing !== false) {
            $db->update('attendance', $attData, ['id' => (int) $existing['id']]);
        } else {
            $attData['created_at'] = date('Y-m-d H:i:s');
            $db->insert('attendance', $attData);
        }

        $this->success('Attendance recorded successfully.', '/attendance?date=' . urlencode($date));
    }
}
