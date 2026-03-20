<?php
declare(strict_types=1);

namespace App\Models;

class ServiceContract extends BaseModel
{
    protected string $table = 'service_contracts';

    /**
     * Find a contract by its unique contract code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `contract_code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Generate the next CONT-XXXX code.
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch(
            "SELECT `contract_code` FROM `{$this->table}` ORDER BY `id` DESC LIMIT 1"
        );

        $next = 1;
        if ($row !== false) {
            $parts = explode('-', $row['contract_code']);
            $next  = (int) end($parts) + 1;
        }

        return 'CONT-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Return a contract joined with customer, product, service type, and branch.
     */
    public function getWithDetails(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT sc.*,
                    CONCAT(cu.`first_name`, ' ', cu.`last_name`) AS customer_name,
                    cu.`phone`          AS customer_phone,
                    cu.`email`          AS customer_email,
                    p.`name`            AS product_name,
                    p.`product_code`    AS product_code,
                    st.`name`           AS service_type_name,
                    st.`code`           AS service_type_code,
                    b.`name`            AS branch_name,
                    u.`name`            AS technician_name
               FROM `{$this->table}` sc
               JOIN `customers`     cu ON cu.`id` = sc.`customer_id`
          LEFT JOIN `products`       p  ON p.`id`  = sc.`product_id`
          LEFT JOIN `service_types`  st ON st.`id` = sc.`service_type_id`
          LEFT JOIN `branches`       b  ON b.`id`  = sc.`branch_id`
          LEFT JOIN `users`          u  ON u.`id`  = sc.`assigned_technician_id`
              WHERE sc.`id` = ?
              LIMIT 1",
            [$id]
        );
    }

    /**
     * Return active contracts whose next service date falls within the next $days days.
     */
    public function getDueForService(int $days = 30): array
    {
        return $this->db->fetchAll(
            "SELECT sc.*,
                    CONCAT(cu.`first_name`, ' ', cu.`last_name`) AS customer_name,
                    cu.`phone`       AS customer_phone,
                    p.`name`         AS product_name,
                    b.`name`         AS branch_name
               FROM `{$this->table}` sc
               JOIN `customers` cu ON cu.`id` = sc.`customer_id`
          LEFT JOIN `products`   p  ON p.`id`  = sc.`product_id`
          LEFT JOIN `branches`   b  ON b.`id`  = sc.`branch_id`
              WHERE sc.`status` = 'active'
                AND sc.`next_service_date` BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
           ORDER BY sc.`next_service_date` ASC",
            [$days]
        );
    }

    /**
     * Return all contracts for a given customer.
     */
    public function getByCustomer(int $customerId): array
    {
        return $this->db->fetchAll(
            "SELECT sc.*,
                    p.`name`      AS product_name,
                    st.`name`     AS service_type_name,
                    b.`name`      AS branch_name
               FROM `{$this->table}` sc
          LEFT JOIN `products`      p  ON p.`id`  = sc.`product_id`
          LEFT JOIN `service_types` st ON st.`id` = sc.`service_type_id`
          LEFT JOIN `branches`      b  ON b.`id`  = sc.`branch_id`
              WHERE sc.`customer_id` = ?
           ORDER BY sc.`created_at` DESC",
            [$customerId]
        );
    }
}
