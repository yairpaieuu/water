<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method'     => 'GET',
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function post(string $path, string $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method'     => 'POST',
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $this->normalizeUri($_SERVER['REQUEST_URI'] ?? '/');

        foreach ($this->routes as $route) {
            $params = $this->matchRoute($route['path'], $uri);

            if ($route['method'] === $method && $params !== null) {
                $this->runMiddleware($route['middleware']);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        $this->notFound();
    }

    private function normalizeUri(string $uri): string
    {
        // Strip query string
        $uri = strtok($uri, '?');

        // Remove script prefix if running from a sub-directory
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        return '/' . ltrim($uri ?: '/', '/');
    }

    private function matchRoute(string $routePath, string $uri): ?array
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            // Return only named captures
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $m) {
            if ($m === 'auth') {
                $this->requireAuth();
            }
        }
    }

    private function requireAuth(): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
    }

    private function callHandler(string $handler, array $params): void
    {
        [$controllerName, $method] = explode('@', $handler, 2);

        $class = "App\\Controllers\\{$controllerName}";

        if (!class_exists($class)) {
            $this->notFound();
            return;
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            $this->notFound();
            return;
        }

        $controller->$method(...array_values($params));
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404 Not Found</title>'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<style>body{font-family:sans-serif;text-align:center;padding:60px;background:#f1f5f9}'
            . 'h1{color:#0ea5e9}a{color:#0ea5e9}</style></head>'
            . '<body><h1>AquaCRM</h1><h2>404 &mdash; Page Not Found</h2>'
            . '<p><a href="/">Return to Dashboard</a></p></body></html>';
        exit;
    }
}
