<?php
namespace App\Core;

use App\Helpers\AyudaAcceso;
use App\Helpers\AyudaAuditoria;
use App\Helpers\AyudaIdioma;

class Controlador
{
    public function __construct() {}

    protected function __(string $clave, string $default = ''): string
    {
        return AyudaIdioma::translate($clave, $default);
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = VIEWS . "/{$view}.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new \Exception("Vista no encontrada: {$view}");
        }
    }

    protected function renderWithLayout(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $data['contentView'] = $view;
        $this->render($layout, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . APP_URL . '/' . $url);
        exit;
    }

    protected function jsonResponse(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getPost(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function getGet(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    protected function requireAuth(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth/login');
        }
    }

    protected function requireAccess(string $module): void
    {
        $this->requireAuth();
        AyudaAcceso::requireAccess($module);
    }

    protected function requireWriteAccess(string $module): void
    {
        $this->requireAuth();
        AyudaAcceso::requireWriteAccess($module);
    }

    protected function checkAccess(string $module): bool
    {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }
        return AyudaAcceso::hasAccess($module);
    }

    protected function audit(string $accion): void
    {
        AyudaAuditoria::logCurrentUser($accion);
    }
}