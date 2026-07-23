<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Helpers\AyudaAcceso;

class ApiControlador extends Controlador
{
    protected function jsonResponse(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function jsonSuccess(mixed $data = null, string $message = ''): void
    {
        $response = ['success' => true];
        if ($data !== null) {
            $response['data'] = $data;
        }
        if ($message !== '') {
            $response['message'] = $message;
        }
        $this->jsonResponse($response);
    }

    protected function jsonError(string $error, int $status = 400, array $errors = []): void
    {
        $response = [
            'success' => false,
            'error'   => $error,
        ];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        $this->jsonResponse($response, $status);
    }

    protected function jsonCreated(mixed $data = null, string $message = 'Recurso creado exitosamente'): void
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->jsonResponse($response, 201);
    }

    protected function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return [];
        }
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonError('JSON mal formado en la petición', 400);
        }
        return $data ?? [];
    }

    protected function requireApiAuth(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->jsonError('No autenticado. Debe iniciar sesión.', 401);
        }
    }

    protected function requireApiAccess(string $module): void
    {
        $this->requireApiAuth();
        if (!AyudaAcceso::hasAccess($module)) {
            $this->jsonError('No tiene permisos para acceder a este módulo.', 403);
        }
    }

    protected function requireApiWriteAccess(string $module): void
    {
        $this->requireApiAuth();
        if (!AyudaAcceso::hasWriteAccess($module)) {
            $this->jsonError('No tiene permisos de escritura para este módulo.', 403);
        }
    }

    protected function validateRequired(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field => $label) {
            $value = $data[$field] ?? '';
            if (is_string($value) && trim($value) === '') {
                $errors[$field] = "{$label} es obligatorio.";
            } elseif ($value === null || $value === '') {
                $errors[$field] = "{$label} es obligatorio.";
            }
        }
        return $errors;
    }

    protected function validateNumeric(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (isset($data[$field]) && $data[$field] !== '' && !is_numeric($data[$field])) {
                $errors[$field] = "{$label} debe ser un valor numérico.";
            }
        }
        return $errors;
    }

    protected function validateEmail(array $data, string $field, string $label): ?string
    {
        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            return "{$label} no tiene un formato válido.";
        }
        return null;
    }

    protected function getParam(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
