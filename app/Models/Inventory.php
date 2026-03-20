<?php
declare(strict_types=1);

namespace App\Models;

class Inventory extends BaseModel
{
    protected string $table = 'inventory_stock';

    /**
     * Return all stock rows for a branch, including product details.
     */
    public function getByBranch(int $branchId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, p.`name` AS product_name, p.`product_code`, p.`category`,
                    p.`brand`, p.`model_number`
               FROM `{$this->table}` s
               JOIN `products` p ON p.`id` = s.`product_id`
              WHERE s.`branch_id` = ?
           ORDER BY p.`name` ASC",
            [$branchId]
        );
    }

    /**
     * Return all stock rows where quantity is at or below min_quantity.
     * Pass $threshold to override per-row min_quantity with a global value.
     */
    public function getLowStock(?int $threshold = null): array
    {
        if ($threshold !== null) {
            return $this->db->fetchAll(
                "SELECT s.*, p.`name` AS product_name, p.`product_code`,
                        b.`name` AS branch_name
                   FROM `{$this->table}` s
                   JOIN `products` p ON p.`id` = s.`product_id`
                   JOIN `branches` b ON b.`id` = s.`branch_id`
                  WHERE s.`quantity` <= ?
               ORDER BY s.`quantity` ASC",
                [$threshold]
            );
        }

        return $this->db->fetchAll(
            "SELECT s.*, p.`name` AS product_name, p.`product_code`,
                    b.`name` AS branch_name
               FROM `{$this->table}` s
               JOIN `products` p ON p.`id` = s.`product_id`
               JOIN `branches` b ON b.`id` = s.`branch_id`
              WHERE s.`quantity` <= s.`min_quantity`
           ORDER BY s.`quantity` ASC"
        );
    }

    /**
     * Add (positive) or subtract (negative) stock for a product/branch pair.
     * Creates the row if it does not yet exist.
     *
     * @return bool  True on success.
     */
    public function adjustStock(int $productId, int $branchId, int $quantityChange): bool
    {
        $row = $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `product_id` = ? AND `branch_id` = ? LIMIT 1",
            [$productId, $branchId]
        );

        if ($row === false) {
            $initial = max(0, $quantityChange);
            return $this->db->insert($this->table, [
                'product_id'   => $productId,
                'branch_id'    => $branchId,
                'quantity'     => $initial,
                'min_quantity' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        $newQty = max(0, (int) $row['quantity'] + $quantityChange);
        return $this->db->update(
            $this->table,
            ['quantity' => $newQty, 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => (int) $row['id']]
        );
    }

    /**
     * Return stock levels for a product across all branches.
     */
    public function getProductStock(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, b.`name` AS branch_name
               FROM `{$this->table}` s
               JOIN `branches` b ON b.`id` = s.`branch_id`
              WHERE s.`product_id` = ?
           ORDER BY b.`name` ASC",
            [$productId]
        );
    }
}
