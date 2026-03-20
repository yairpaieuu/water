<?php
declare(strict_types=1);

namespace App\Models;

class Sale extends BaseModel
{
    protected string $table = 'sales';

    /**
     * Find a sale by its unique sale code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `sale_code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Generate the next SALE-XXXX code.
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch(
            "SELECT `sale_code` FROM `{$this->table}` ORDER BY `id` DESC LIMIT 1"
        );

        $next = 1;
        if ($row !== false) {
            $parts = explode('-', $row['sale_code']);
            $next  = (int) end($parts) + 1;
        }

        return 'SALE-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Return a sale joined with customer and branch details.
     */
    public function getWithCustomer(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT s.*,
                    CONCAT(c.`first_name`, ' ', c.`last_name`) AS customer_name,
                    c.`email`    AS customer_email,
                    c.`phone`    AS customer_phone,
                    b.`name`     AS branch_name
               FROM `{$this->table}` s
               JOIN `customers` c ON c.`id` = s.`customer_id`
          LEFT JOIN `branches`  b ON b.`id` = s.`branch_id`
              WHERE s.`id` = ?
              LIMIT 1",
            [$id]
        );
    }

    /**
     * Return all line items for a sale, including product names.
     */
    public function getItems(int $saleId): array
    {
        return $this->db->fetchAll(
            "SELECT si.*, p.`name` AS product_name, p.`product_code`
               FROM `sale_items` si
               JOIN `products`   p  ON p.`id` = si.`product_id`
              WHERE si.`sale_id` = ?
           ORDER BY si.`id` ASC",
            [$saleId]
        );
    }

    /**
     * Create a sale with its line items inside a transaction.
     * Also decrements inventory stock for each item.
     *
     * @param  array<string, mixed>   $saleData  Data for the `sales` row.
     * @param  array<int, array<string, mixed>>  $items     Each element: product_id, quantity, unit_price, discount, total.
     * @return int|false  The new sale ID, or false on failure.
     */
    public function createWithItems(array $saleData, array $items): int|false
    {
        $this->db->beginTransaction();
        try {
            $saleId = $this->create($saleData);
            if ($saleId === false) {
                $this->db->rollback();
                return false;
            }

            $inventory = new Inventory();
            $now       = date('Y-m-d H:i:s');

            foreach ($items as $item) {
                $item['sale_id']    = $saleId;
                $item['created_at'] = $now;

                $inserted = $this->db->insert('sale_items', $item);
                if (!$inserted) {
                    $this->db->rollback();
                    return false;
                }

                if (isset($saleData['branch_id'])) {
                    $inventory->adjustStock(
                        (int) $item['product_id'],
                        (int) $saleData['branch_id'],
                        -(int) $item['quantity']
                    );
                }
            }

            $this->db->commit();
            return $saleId;
        } catch (\Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Return total revenue per month for a given year.
     * Returns an array indexed 1–12 with the total for each month.
     *
     * @return array<int, float>
     */
    public function getMonthlyRevenue(int $year): array
    {
        $rows = $this->db->fetchAll(
            "SELECT MONTH(`sale_date`) AS month, SUM(`total`) AS revenue
               FROM `{$this->table}`
              WHERE YEAR(`sale_date`) = ?
                AND `status` != 'cancelled'
           GROUP BY MONTH(`sale_date`)",
            [$year]
        );

        $result = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $result[(int) $row['month']] = (float) $row['revenue'];
        }
        return $result;
    }

    /**
     * Return the most recent $limit sales with customer name.
     */
    public function recentSales(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT s.*,
                    CONCAT(c.`first_name`, ' ', c.`last_name`) AS customer_name
               FROM `{$this->table}` s
               JOIN `customers` c ON c.`id` = s.`customer_id`
           ORDER BY s.`created_at` DESC
              LIMIT " . max(1, $limit)
        );
    }
}
