<?php
declare(strict_types=1);

namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'users';

    /**
     * Roles available in the system.
     */
    public const ROLES = ['admin', 'manager', 'technician', 'sales'];

    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `email` = ? LIMIT 1",
            [$email]
        );
    }

    /**
     * Find a user by their username.
     */
    public function findByUsername(string $username): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `username` = ? LIMIT 1",
            [$username]
        );
    }

    /**
     * Find a user by username or email (for flexible login).
     */
    public function findByCredential(string $login): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}`
              WHERE `username` = ? OR `email` = ?
              LIMIT 1",
            [$login, $login]
        );
    }

    /**
     * Stamp the last_login timestamp for the given user.
     */
    public function updateLastLogin(int $id): void
    {
        $this->db->update(
            $this->table,
            ['last_login' => date('Y-m-d H:i:s')],
            ['id' => $id]
        );
    }

    /**
     * Return a user row joined with their branch name.
     */
    public function getWithBranch(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT u.*, b.name AS branch_name
               FROM `{$this->table}` u
          LEFT JOIN `branches` b ON b.id = u.branch_id
              WHERE u.id = ?
              LIMIT 1",
            [$id]
        );
    }

    /**
     * Create a new user, hashing the password automatically.
     */
    public function createUser(array $data): int|false
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $this->create($data);
    }

    /**
     * Change a user's password.
     */
    public function changePassword(int $id, string $plainPassword): bool
    {
        return $this->update($id, [
            'password' => password_hash($plainPassword, PASSWORD_BCRYPT),
        ]);
    }
}
