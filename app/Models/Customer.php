<?php
declare(strict_types=1);

namespace App\Models;

class Customer extends BaseModel
{
    protected string $table = 'customers';

    /**
     * Find a customer by their unique customer code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `customer_code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Search customers by name, email, or phone.
     * Optionally filter by branch.
     */
    public function search(string $term, ?int $branchId = null): array
    {
        $like   = '%' . $term . '%';
        $params = [$like, $like, $like];
        $sql    = "SELECT * FROM `{$this->table}`
                   WHERE (`first_name` LIKE ? OR `last_name` LIKE ? OR `email` LIKE ?
                          OR `phone` LIKE ?)";
        $params[] = $like; // phone

        if ($branchId !== null) {
            $sql     .= ' AND `branch_id` = ?';
            $params[] = $branchId;
        }

        $sql .= ' ORDER BY `first_name` ASC, `last_name` ASC';

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Generate the next CUST-XXXX code.
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch(
            "SELECT `customer_code` FROM `{$this->table}` ORDER BY `id` DESC LIMIT 1"
        );

        $next = 1;
        if ($row !== false) {
            $parts = explode('-', $row['customer_code']);
            $next  = (int) end($parts) + 1;
        }

        return 'CUST-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Return a customer joined with their branch name.
     */
    public function getWithBranch(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT c.*, b.`name` AS branch_name
               FROM `{$this->table}` c
          LEFT JOIN `branches` b ON b.`id` = c.`branch_id`
              WHERE c.`id` = ?
              LIMIT 1",
            [$id]
        );
    }

    /**
     * Return an array of branch_id => customer_count.
     *
     * @return array<int, int>
     */
    public function countByBranch(): array
    {
        $rows   = $this->db->fetchAll(
            "SELECT `branch_id`, COUNT(*) AS cnt FROM `{$this->table}` GROUP BY `branch_id`"
        );
        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row['branch_id']] = (int) $row['cnt'];
        }
        return $result;
    }
}
