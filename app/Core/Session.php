<?php
declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = require CONFIG_PATH . '/app.php';
        $cfg    = $config['session'];

        session_name($cfg['name']);

        session_set_cookie_params([
            'lifetime' => $cfg['lifetime'],
            'path'     => '/',
            'domain'   => '',
            'secure'   => $cfg['secure'],
            'httponly' => $cfg['httponly'],
            'samesite' => $cfg['samesite'] ?? 'Lax',
        ]);

        session_start();
        self::$started = true;

        // Rotate session ID periodically to reduce fixation risk
        if (!isset($_SESSION['_initiated'])) {
            session_regenerate_id(true);
            $_SESSION['_initiated'] = true;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Store a one-time flash message.
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Retrieve and remove a flash message.
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Regenerate the session ID (call on privilege escalation / login).
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
        self::$started = false;
    }
}
