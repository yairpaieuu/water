<?php
declare(strict_types=1);

namespace App\Core;

class CSRF
{
    private const SESSION_KEY = '_csrf_token';
    private const TOKEN_LENGTH = 32;

    /**
     * Generate (or reuse) a CSRF token for the current session.
     */
    public static function generate(): string
    {
        if (!Session::has(self::SESSION_KEY)) {
            Session::set(self::SESSION_KEY, bin2hex(random_bytes(self::TOKEN_LENGTH)));
        }
        return Session::get(self::SESSION_KEY);
    }

    /**
     * Verify that the supplied token matches the session token.
     * Regenerates the token after a successful verification.
     */
    public static function verify(string $token): bool
    {
        $stored = Session::get(self::SESSION_KEY, '');

        if (!hash_equals($stored, $token)) {
            return false;
        }

        // Rotate the token after each successful verification
        Session::set(self::SESSION_KEY, bin2hex(random_bytes(self::TOKEN_LENGTH)));

        return true;
    }

    /**
     * Render an HTML hidden input containing the current CSRF token.
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::generate(), ENT_QUOTES, 'UTF-8');
        return "<input type=\"hidden\" name=\"_csrf_token\" value=\"{$token}\">";
    }
}
