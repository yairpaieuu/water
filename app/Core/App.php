<?php
declare(strict_types=1);

namespace App\Core;

class App
{
    private static array $config = [];

    public static function run(): void
    {
        self::loadConfig();
        self::configureEnvironment();
        Session::start();
        $router = self::buildRouter();
        $router->dispatch();
    }

    public static function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    private static function loadConfig(): void
    {
        self::$config = require CONFIG_PATH . '/app.php';
    }

    private static function configureEnvironment(): void
    {
        $timezone = self::config('app.timezone', 'UTC');
        date_default_timezone_set($timezone);

        if (self::config('app.env') === 'production') {
            error_reporting(0);
            ini_set('display_errors', '0');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }
    }

    private static function buildRouter(): Router
    {
        $router = new Router();

        // Auth routes
        $router->get('/login', 'AuthController@showLogin');
        $router->post('/login', 'AuthController@login');
        $router->get('/logout', 'AuthController@logout');

        // Dashboard
        $router->get('/', 'DashboardController@index', ['auth']);
        $router->get('/dashboard', 'DashboardController@index', ['auth']);

        return $router;
    }
}
