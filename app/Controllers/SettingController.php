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
use App\Models\User;

class SettingController extends BaseController
{
    public function index(): void
    {
        $settings = $this->loadSettings();

        $this->render('settings.index', [
            'pageTitle' => 'Settings',
            'settings'  => $settings,
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function update(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $allowed = [
            'app_name', 'app_email', 'app_phone', 'app_address',
            'currency', 'timezone', 'date_format',
        ];

        $db = Database::getInstance();
        foreach ($allowed as $key) {
            $value = $request->post($key, '');
            $existing = $db->fetch(
                "SELECT `id` FROM `settings` WHERE `key` = ? LIMIT 1",
                [$key]
            );
            if ($existing !== false) {
                $db->update('settings', ['value' => $value, 'updated_at' => date('Y-m-d H:i:s')], ['key' => $key]);
            } else {
                $db->insert('settings', [
                    'key'        => $key,
                    'value'      => $value,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->success('Settings updated successfully.', '/settings');
    }

    public function indexUsers(): void
    {
        $request  = new Request();
        $role     = $request->get('role', '');
        $branchId = (int) $request->get('branch_id', 0);

        $db     = Database::getInstance();
        $where  = ['u.`status` != ?'];
        $params = ['deleted'];

        if ($role !== '') {
            $where[]  = 'u.`role` = ?';
            $params[] = $role;
        }
        if ($branchId > 0) {
            $where[]  = 'u.`branch_id` = ?';
            $params[] = $branchId;
        }

        $whereSql = ' WHERE ' . implode(' AND ', $where);
        $users    = $db->fetchAll(
            "SELECT u.*, b.`name` AS branch_name
               FROM `users` u
          LEFT JOIN `branches` b ON b.`id` = u.`branch_id`"
            . $whereSql
            . " ORDER BY u.`name` ASC",
            $params
        );

        $this->render('settings.users', [
            'pageTitle' => 'User Management',
            'users'     => $users,
            'branches'  => (new Branch())->getActive(),
            'roles'     => User::ROLES,
            'role'      => $role,
            'branchId'  => $branchId,
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function createUser(): void
    {
        $this->render('settings.create_user', [
            'pageTitle' => 'Add User',
            'branches'  => (new Branch())->getActive(),
            'roles'     => User::ROLES,
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function storeUser(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $data = [
            'name'      => $request->post('name', ''),
            'email'     => $request->post('email', ''),
            'password'  => $request->post('password', ''),
            'role'      => $request->post('role', 'sales'),
            'branch_id' => (int) $request->post('branch_id', 0),
            'status'    => 'active',
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'name'     => 'required|max:150',
            'email'    => 'required|email|unique:users:email',
            'password' => 'required|min:8',
            'role'     => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        if (!in_array($data['role'], User::ROLES, true)) {
            Session::flash('error', 'Invalid role selected.');
            $this->back();
        }

        if ($data['branch_id'] === 0) {
            unset($data['branch_id']);
        }

        $userModel = new User();
        $id        = $userModel->createUser($data);

        if ($id === false) {
            $this->error('Failed to create user. Please try again.');
        }

        $this->success('User created successfully.', '/users');
    }

    public function updateUser(string $id): void
    {
        $userId  = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $userModel = new User();
        $target    = $userModel->find($userId);

        if ($target === false) {
            $this->error('User not found.', '/users');
        }

        $role     = $request->post('role', $target['role']);
        $status   = $request->post('status', $target['status']);
        $branchId = (int) $request->post('branch_id', 0);
        $name     = $request->post('name', $target['name']);

        if (!in_array($role, User::ROLES, true)) {
            $this->error('Invalid role selected.', '/users');
        }

        $allowedStatuses = ['active', 'inactive'];
        if (!in_array($status, $allowedStatuses, true)) {
            $this->error('Invalid status.', '/users');
        }

        $updateData = [
            'name'   => $name,
            'role'   => $role,
            'status' => $status,
        ];

        if ($branchId > 0) {
            $updateData['branch_id'] = $branchId;
        }

        // Optionally update password if provided
        $newPassword = $request->post('password', '');
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                $this->error('Password must be at least 8 characters.', '/users');
            }
            $userModel->changePassword($userId, $newPassword);
        }

        $userModel->update($userId, $updateData);
        $this->success('User updated successfully.', '/users');
    }

    public function deleteUser(string $id): void
    {
        $userId  = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $target = (new User())->find($userId);
        if ($target === false) {
            $this->error('User not found.', '/users');
        }

        // Prevent self-deletion
        if ($userId === Auth::id()) {
            $this->error('You cannot delete your own account.', '/users');
        }

        // Soft delete: set status to inactive
        (new User())->update($userId, ['status' => 'inactive']);
        $this->success('User deactivated successfully.', '/users');
    }

    /**
     * Load all settings from the `settings` table as a key=>value map.
     *
     * @return array<string, string>
     */
    private function loadSettings(): array
    {
        try {
            $rows   = Database::getInstance()->fetchAll("SELECT `key`, `value` FROM `settings`");
            $result = [];
            foreach ($rows as $row) {
                $result[$row['key']] = $row['value'];
            }
            return $result;
        } catch (\Throwable) {
            return [];
        }
    }
}
