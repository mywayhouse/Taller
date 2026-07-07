<?php
// ============================================================
// Router.php — Enrutador dinámico de URLs amigables
// ============================================================
// Toma la URL solicitada (ej: "clientes/editar/5") y la
// descompone en tres partes:
//   1. Controlador  (ej: "ClientesController")
//   2. Método       (ej: "editar")
//   3. Parámetros   (ej: [5])
//
// Si no se especifica controlador, carga el controlador
// por defecto (DashboardController).
// ============================================================
namespace App\core;
/**
 * Router — Despachador de peticiones HTTP
 */
class Router
{
    /**
     * Controlador por defecto cuando la URL está vacía.
     */
    private string $defaultController = 'DashboardController';

    /**
     * Método por defecto cuando solo se especifica controlador.
     */
    private string $defaultMethod = 'index';

    /**
     * Analiza la URL y ejecuta el método del controlador
     * correspondiente.
     *
     * @param string $url Ruta limpia (sin / inicial ni final).
     */
    public function dispatch(string $url): void
    {
        // --------------------------------------------------
        // 1. Sanitizar y limpiar la URL
        // --------------------------------------------------
        // Elimina caracteres peligrosos y espacios extras.
        // Convierte "/" en separadores de array.
        $url = filter_var(trim($url), FILTER_SANITIZE_URL);
        $url = rtrim($url, '/');
        $urlParts = !empty($url) ? explode('/', $url) : [];

        // --------------------------------------------------
        // 2. Determinar el Controlador
        // --------------------------------------------------
        // Si existe un primer segmento, lo usa como controlador.
        // Formato: "clientes" -> "ClientesController"
        $controllerName = $this->defaultController;

        if (!empty($urlParts[0])) {
            // Convertir a formato PascalCase: "clientes" -> "Clientes"
            $controllerName = ucfirst(strtolower($urlParts[0])) . 'Controller';
            array_shift($urlParts); // Eliminar el controlador del array
        }

        // --------------------------------------------------
        // 3. Determinar el Método
        // --------------------------------------------------
        // El segundo segmento (si existe) es el nombre del método.
        // Formato: "listar" -> "listar()"
        $methodName = $this->defaultMethod;

        if (!empty($urlParts[0])) {
            $methodName = strtolower($urlParts[0]);
            array_shift($urlParts); // Eliminar el método del array
        }

        // --------------------------------------------------
        // 4. Los segmentos restantes son parámetros
        // --------------------------------------------------
        $params = $urlParts;

        // --------------------------------------------------
        // 5. Verificar que el controlador existe
        // --------------------------------------------------
        $controllerClass = "App\\controllers\\{$controllerName}";
        $controllerFile = APP . "/controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            $this->showError(404, "Controlador '{$controllerName}' no encontrado.");
            return;
        }

        if (!class_exists($controllerClass)) {
            $this->showError(500, "Clase '{$controllerClass}' no definida en el archivo.");
            return;
        }

        // --------------------------------------------------
        // 6. Instanciar el controlador y llamar al método
        // --------------------------------------------------
        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            $this->showError(404, "Método '{$methodName}' no existe en {$controllerName}.");
            return;
        }

        // --------------------------------------------------
        // 7. Ejecutar el método con los parámetros
        // --------------------------------------------------
        call_user_func_array([$controller, $methodName], $params);
    }

    /**
     * Muestra una página de error personalizada.
     *
     * @param int    $code    Código HTTP (404, 500, etc.)
     * @param string $message Mensaje descriptivo del error.
     */
    private function showError(int $code, string $message): void
    {
        http_response_code($code);
        $title = "Error {$code}";

        // Cargar una vista de error si existe
        $errorView = VIEWS . "/errors/{$code}.php";
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            // Fallback: mostrar mensaje en texto plano
            echo "<h1>{$title}</h1>";
            echo "<p>{$message}</p>";
        }
        exit;
    }
}
