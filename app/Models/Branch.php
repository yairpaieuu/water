<?php
declare(strict_types=1);

namespace App\Models;

class Branch extends BaseModel
{
    protected string $table = 'branches';

    /**
     * Return all active branches ordered by name.
     */
    public function getActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE `status` = 'active' ORDER BY `name` ASC"
        );
    }
}
