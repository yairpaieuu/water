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
            // Accept only simple "column [ASC|DESC]" or comma-separated pairs.
            // Each segment must be: word_chars optionally followed by ASC or DESC.
            $segments = array_map('trim', explode(',', $orderBy));
            $safeParts = [];
            foreach ($segments as $seg) {
                if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)(?:\s+(ASC|DESC))?$/i', $seg, $m)) {
                    $safeParts[] = '`' . $m[1] . '`' . (isset($m[2]) ? ' ' . strtoupper($m[2]) : '');
                }
                // Silently discard any segment that doesn't match the whitelist pattern
            }
            if (!empty($safeParts)) {
                $sql .= ' ORDER BY ' . implode(', ', $safeParts);
            }
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
