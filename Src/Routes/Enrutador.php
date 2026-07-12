<?php
namespace App\Routes;

class Enrutador
{
    private string $defaultController = 'ControladorPanel';
    private string $defaultMethod = 'index';

    public function dispatch(string $url): void
    {
        $url = filter_var(trim($url), FILTER_SANITIZE_URL);
        $url = rtrim($url, '/');
        $urlParts = !empty($url) ? explode('/', $url) : [];

        $controllerName = $this->defaultController;

        if (!empty($urlParts[0])) {
            $url0 = strtolower($urlParts[0]);
            
            if ($url0 === 'dashboard') {
                $controllerName = 'ControladorPanel';
            } else {
                $controllerName = 'Controlador' . ucfirst($url0);
            }
            array_shift($urlParts);
        }

        $methodName = $this->defaultMethod;

        if (!empty($urlParts[0])) {
            $methodName = strtolower($urlParts[0]);
            array_shift($urlParts);
        }

        $params = $urlParts;

        $controllerClass = "App\\Controllers\\{$controllerName}";
        $controllerFile = APP . "/Controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            $this->showError(404, "Controlador '{$controllerName}' no encontrado.");
            return;
        }

        if (!class_exists($controllerClass)) {
            $this->showError(500, "Clase '{$controllerClass}' no definida en el archivo.");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            $this->showError(404, "Método '{$methodName}' no existe en {$controllerName}.");
            return;
        }

        call_user_func_array([$controller, $methodName], $params);
    }

    private function showError(int $code, string $message): void
    {
        /* --- CÓDIGO TEMPORAL PARA DEPURAR ---
        echo "<div style='background:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; font-family:sans-serif;'>";
        echo "<h2>Error de Enrutamiento ({$code})</h2>";
        echo "<p><strong>Motivo exacto:</strong> {$message}</p>";
        echo "</div>";
        exit; // Detenemos la ejecución aquí para ver el error
        */
        http_response_code($code);
        $title = "Error {$code}";
        $errorView = VIEWS . "/errors/{$code}.php";
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "<h1>{$title}</h1>";
            echo "<p>{$message}</p>";
        }
        exit;
    }
}