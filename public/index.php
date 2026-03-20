<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('VIEW_PATH', APP_PATH . '/Views');

// Autoloader
spl_autoload_register(function (string $class): void {
    $namespaceMap = [
        'App\\Core\\'        => APP_PATH . '/Core/',
        'App\\Controllers\\' => APP_PATH . '/Controllers/',
        'App\\Models\\'      => APP_PATH . '/Models/',
    ];

    foreach ($namespaceMap as $namespace => $path) {
        if (str_starts_with($class, $namespace)) {
            $relative = substr($class, strlen($namespace));
            $file = $path . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Escape helper for views
if (!function_exists('e')) {
    function e(mixed $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Bootstrap the application
require_once APP_PATH . '/Core/App.php';
\App\Core\App::run();
