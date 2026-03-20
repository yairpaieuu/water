<?php
declare(strict_types=1);

namespace App\Models;

class ServiceType extends BaseModel
{
    protected string $table = 'service_types';

    /**
     * Find a service type by its unique code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Return all service types for a given category.
     *
     * @param  string $category  'domestic' or 'commercial'
     */
    public function getByCategory(string $category): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE `category` = ? ORDER BY `interval_months` ASC",
            [$category]
        );
    }
}
