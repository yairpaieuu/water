<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    protected Database $db;
    protected string $table    = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a single record by primary key.
     */
    public function find(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1",
            [$id]
        );
    }

    /**
     * Find all records, with optional conditions, ordering, and limit.
     *
     * @param array<string, mixed> $conditions  e.g. ['status' => 'active']
     */
    public function findAll(array $conditions = [], string $orderBy = '', int $limit = 0): array
    {
        $sql    = "SELECT * FROM `{$this->table}`";
        $params = [];

        if (!empty($conditions)) {
            $parts = array_map(
                fn(string $col) => "`{$col}` = ?",
                array_keys($conditions)
            );
            $sql   .= ' WHERE ' . implode(' AND ', $parts);
            $params = array_values($conditions);
        }

        if ($orderBy !== '') {
            // Whitelist: only word characters and comma/space/ASC/DESC
            $safe = preg_replace('/[^a-zA-Z0-9_,\s]/', '', $orderBy);
            $sql .= " ORDER BY {$safe}";
        }

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Insert a new record and return its ID.
     */
    public function create(array $data): int|false
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $success = $this->db->insert($this->table, $data);
        return $success ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Update a record by primary key.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update(
            $this->table,
            $data,
            [$this->primaryKey => $id]
        );
    }

    /**
     * Delete a record by primary key.
     */
    public function delete(int $id): bool
    {
        return $this->db->delete(
            $this->table,
            [$this->primaryKey => $id]
        );
    }

    /**
     * Count records matching optional conditions.
     *
     * @param array<string, mixed> $conditions
     */
    public function count(array $conditions = []): int
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM `{$this->table}`";
        $params = [];

        if (!empty($conditions)) {
            $parts = array_map(
                fn(string $col) => "`{$col}` = ?",
                array_keys($conditions)
            );
            $sql   .= ' WHERE ' . implode(' AND ', $parts);
            $params = array_values($conditions);
        }

        $row = $this->db->fetch($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }
}
