<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    private const SESSION_KEY = '_auth_user';

    /**
     * Attempt to authenticate a user by username/email and password.
     */
    public static function login(string $credential, string $password): bool
    {
        $user = (new User())->findByCredential($credential);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if (($user['status'] ?? 'active') !== 'active') {
            return false;
        }

        // Regenerate session ID to prevent session fixation
        Session::regenerate();

        // Remove sensitive field before storing in session
        unset($user['password']);
        Session::set(self::SESSION_KEY, $user);

        // Record last login timestamp
        (new User())->updateLastLogin((int) $user['id']);

        return true;
    }

    public static function logout(): void
    {
        Session::remove(self::SESSION_KEY);
        Session::regenerate();
    }

    /**
     * Check whether the current request is authenticated.
     */
    public static function check(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    /**
     * Return the authenticated user array, or null.
     */
    public static function user(): ?array
    {
        return Session::get(self::SESSION_KEY);
    }

    /**
     * Return the authenticated user's ID, or null.
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int) $user['id'] : null;
    }

    /**
     * Check whether the authenticated user has a specific role.
     */
    public static function hasRole(string $role): bool
    {
        $user = self::user();
        return $user !== null && ($user['role'] ?? '') === $role;
    }
}
