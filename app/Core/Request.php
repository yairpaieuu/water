<?php
declare(strict_types=1);

namespace App\Core;

class Request
{
    /**
     * Retrieve a value from $_GET, with optional default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    /**
     * Retrieve a value from $_POST, with optional default.
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $default;
    }

    /**
     * Retrieve an uploaded file entry from $_FILES.
     */
    public function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        return strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function ip(): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                // Take first IP if comma-separated list
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    /**
     * Return only the specified keys from POST + GET merged input.
     * @param string[] $keys
     * @return array<string, mixed>
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Return all POST + GET merged input (POST takes precedence).
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $merged = array_merge($_GET, $_POST);
        return array_map([$this, 'sanitize'], $merged);
    }

    /**
     * Recursively trim strings; leave non-strings untouched.
     */
    private function sanitize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        if (is_string($value)) {
            return trim($value);
        }
        return $value;
    }
}
