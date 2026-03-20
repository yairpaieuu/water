<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require CONFIG_PATH . '/app.php';
        $db     = $config['db'];

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['dbname'],
            $db['charset']
        );

        $this->pdo = new PDO($dsn, $db['username'], $db['password'], $db['options']);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Execute a query and return the PDOStatement.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row.
     */
    public function fetch(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Insert a row into a table.
     * @param array<string, mixed> $data
     */
    public function insert(string $table, array $data): bool
    {
        $table   = $this->quoteIdentifier($table);
        $columns = implode(', ', array_map([$this, 'quoteIdentifier'], array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        return $this->query($sql, array_values($data))->rowCount() > 0;
    }

    /**
     * Update rows in a table.
     * @param array<string, mixed> $data
     * @param array<string, mixed> $where        WHERE clause as ['column' => value, ...]
     * @param array<mixed>         $whereParams  (unused – kept for BC; derived from $where)
     */
    public function update(string $table, array $data, array $where, array $whereParams = []): bool
    {
        $table = $this->quoteIdentifier($table);

        $setParts = array_map(
            fn(string $col) => $this->quoteIdentifier($col) . ' = ?',
            array_keys($data)
        );
        $whereParts = array_map(
            fn(string $col) => $this->quoteIdentifier($col) . ' = ?',
            array_keys($where)
        );

        $sql    = "UPDATE {$table} SET " . implode(', ', $setParts)
                . " WHERE " . implode(' AND ', $whereParts);
        $params = array_merge(array_values($data), array_values($where));

        return $this->query($sql, $params)->rowCount() > 0;
    }

    /**
     * Delete rows from a table.
     * @param array<string, mixed> $where WHERE clause as ['column' => value, ...]
     */
    public function delete(string $table, array $where, array $params = []): bool
    {
        $table = $this->quoteIdentifier($table);

        $whereParts = array_map(
            fn(string $col) => $this->quoteIdentifier($col) . ' = ?',
            array_keys($where)
        );

        $sql        = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereParts);
        $bindParams = empty($params) ? array_values($where) : $params;

        return $this->query($sql, $bindParams)->rowCount() > 0;
    }

    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    private function quoteIdentifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    // Prevent cloning/unserialization of singleton
    private function __clone() {}
}
