<?php
declare(strict_types=1);

namespace App\Models;

class ServiceJob extends BaseModel
{
    protected string $table = 'service_jobs';

    /**
     * Find a job by its unique job code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `job_code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Generate the next JOB-XXXX code.
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch(
            "SELECT `job_code` FROM `{$this->table}` ORDER BY `id` DESC LIMIT 1"
        );

        $next = 1;
        if ($row !== false) {
            $parts = explode('-', $row['job_code']);
            $next  = (int) end($parts) + 1;
        }

        return 'JOB-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Return a job joined with customer, contract, branch, and assigned user.
     */
    public function getWithDetails(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT sj.*,
                    CONCAT(cu.`first_name`, ' ', cu.`last_name`) AS customer_name,
                    cu.`phone`       AS customer_phone,
                    sc.`contract_code`,
                    b.`name`         AS branch_name,
                    u.`name`         AS assigned_to_name
               FROM `{$this->table}` sj
               JOIN `customers`         cu ON cu.`id` = sj.`customer_id`
          LEFT JOIN `service_contracts` sc ON sc.`id` = sj.`contract_id`
          LEFT JOIN `branches`          b  ON b.`id`  = sj.`branch_id`
          LEFT JOIN `users`             u  ON u.`id`  = sj.`assigned_to`
              WHERE sj.`id` = ?
              LIMIT 1",
            [$id]
        );
    }

    /**
     * Return pending and assigned jobs, optionally filtered by branch.
     */
    public function getPending(?int $branchId = null): array
    {
        $params = ['pending', 'assigned'];
        $sql    = "SELECT sj.*,
                          CONCAT(cu.`first_name`, ' ', cu.`last_name`) AS customer_name,
                          b.`name` AS branch_name,
                          u.`name` AS assigned_to_name
                     FROM `{$this->table}` sj
                     JOIN `customers` cu ON cu.`id` = sj.`customer_id`
                LEFT JOIN `branches`  b  ON b.`id`  = sj.`branch_id`
                LEFT JOIN `users`     u  ON u.`id`  = sj.`assigned_to`
                    WHERE sj.`status` IN (?, ?)";

        if ($branchId !== null) {
            $sql     .= ' AND sj.`branch_id` = ?';
            $params[] = $branchId;
        }

        $sql .= ' ORDER BY sj.`priority` DESC, sj.`scheduled_date` ASC';

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Return all jobs assigned to a given technician (user).
     */
    public function getByTechnician(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT sj.*,
                    CONCAT(cu.`first_name`, ' ', cu.`last_name`) AS customer_name,
                    b.`name` AS branch_name
               FROM `{$this->table}` sj
               JOIN `customers` cu ON cu.`id` = sj.`customer_id`
          LEFT JOIN `branches`  b  ON b.`id`  = sj.`branch_id`
              WHERE sj.`assigned_to` = ?
           ORDER BY sj.`scheduled_date` DESC",
            [$userId]
        );
    }

    /**
     * Return the most recent $limit jobs with customer and branch info.
     */
    public function recentJobs(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT sj.*,
                    CONCAT(cu.`first_name`, ' ', cu.`last_name`) AS customer_name,
                    b.`name` AS branch_name
               FROM `{$this->table}` sj
               JOIN `customers` cu ON cu.`id` = sj.`customer_id`
          LEFT JOIN `branches`  b  ON b.`id`  = sj.`branch_id`
           ORDER BY sj.`created_at` DESC
              LIMIT " . max(1, $limit)
        );
    }
}
