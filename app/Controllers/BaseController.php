<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;

abstract class BaseController
{
    /**
     * Render a view within a layout.
     *
     * @param string               $view   Dot-notation relative to app/Views/, e.g. 'dashboard.index'
     * @param array<string, mixed> $data   Variables to extract into the view scope
     * @param string               $layout Layout file under app/Views/layouts/, without extension
     */
    protected function render(string $view, array $data = [], string $layout = 'app'): void
    {
        // Build absolute path from dot notation
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo htmlspecialchars("View not found: {$viewFile}", ENT_QUOTES, 'UTF-8');
            exit;
        }

        // Make data variables available inside the view
        extract($data, EXTR_SKIP);

        // Capture view output
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Wrap in layout
        $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';
        if (is_file($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Send a JSON response.
     *
     * @param array<mixed>|object $data
     */
    protected function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    /**
     * Redirect back to the previous URL (or a fallback).
     */
    protected function back(string $fallback = '/'): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($referer);
    }

    /**
     * Flash a success message and redirect.
     */
    protected function success(string $message, string $redirect = '/'): never
    {
        Session::flash('success', $message);
        $this->redirect($redirect);
    }

    /**
     * Flash an error message and redirect back.
     */
    protected function error(string $message, string $redirect = ''): never
    {
        Session::flash('error', $message);
        if ($redirect !== '') {
            $this->redirect($redirect);
        }
        $this->back();
    }
}
