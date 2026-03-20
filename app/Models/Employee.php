<?php
declare(strict_types=1);

namespace App\Models;

class Employee extends BaseModel
{
    protected string $table = 'employees';

    /**
     * Find an employee by their unique employee code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `employee_code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Generate the next EMP-XXXX code.
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch(
            "SELECT `employee_code` FROM `{$this->table}` ORDER BY `id` DESC LIMIT 1"
        );

        $next = 1;
        if ($row !== false) {
            $parts = explode('-', $row['employee_code']);
            $next  = (int) end($parts) + 1;
        }

        return 'EMP-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Return an employee joined with their branch name.
     */
    public function getWithBranch(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT e.*, b.`name` AS branch_name
               FROM `{$this->table}` e
          LEFT JOIN `branches` b ON b.`id` = e.`branch_id`
              WHERE e.`id` = ?
              LIMIT 1",
            [$id]
        );
    }

    /**
     * Return active employees in the technical department.
     * Optionally filter by branch.
     */
    public function getTechnicians(?int $branchId = null): array
    {
        $params = ['technical', 'active'];
        $sql    = "SELECT e.*, b.`name` AS branch_name
                     FROM `{$this->table}` e
                LEFT JOIN `branches` b ON b.`id` = e.`branch_id`
                    WHERE e.`department` = ? AND e.`status` = ?";

        if ($branchId !== null) {
            $sql     .= ' AND e.`branch_id` = ?';
            $params[] = $branchId;
        }

        $sql .= ' ORDER BY e.`first_name` ASC, e.`last_name` ASC';

        return $this->db->fetchAll($sql, $params);
    }
}
