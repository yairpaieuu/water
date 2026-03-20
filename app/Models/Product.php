<?php
declare(strict_types=1);

namespace App\Models;

class Product extends BaseModel
{
    protected string $table = 'products';

    /**
     * Return all active products ordered by name.
     */
    public function getActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE `status` = 'active' ORDER BY `name` ASC"
        );
    }

    /**
     * Find a product by its unique product code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `product_code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Generate the next PROD-XXXX code.
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch(
            "SELECT `product_code` FROM `{$this->table}` ORDER BY `id` DESC LIMIT 1"
        );

        $next = 1;
        if ($row !== false) {
            $parts = explode('-', $row['product_code']);
            $next  = (int) end($parts) + 1;
        }

        return 'PROD-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
