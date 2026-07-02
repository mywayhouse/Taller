<?php
// ============================================================
// Controller.php — Controlador Base
// ============================================================
// Todos los controladores del sistema DEBEN extender esta
// clase. Proporciona métodos utilitarios comunes como:
//   - render()      -> Cargar una vista con datos.
//   - redirect()    -> Redirigir a otra URL.
//   - jsonResponse()-> Devolver JSON (para peticiones AJAX).
//   - isPost()      -> Verificar si la petición es POST.
// ============================================================

/**
 * Clase base para todos los Controladores de la aplicación.
 */
class Controller
{
    /**
     * Renderiza una vista y le pasa variables.
     *
     * @param string $view  Ruta relativa desde /views/ (ej: "clientes/listar").
     * @param array  $data  Arreglo asociativo de datos para la vista.
     */
    protected function render(string $view, array $data = []): void
    {
        // --------------------------------------------------
        // Extraer el array $data como variables sueltas
        // para usarlas directamente en la vista.
        // Ej: ["titulo" => "Hola"] -> $titulo = "Hola"
        // --------------------------------------------------
        extract($data);

        // --------------------------------------------------
        // Incluir la vista solicitada
        // --------------------------------------------------
        $viewFile = VIEWS . "/{$view}.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new \Exception("Vista no encontrada: {$view}");
        }
    }

    /**
     * Renderiza una vista DENTRO del layout principal.
     * Útil para mantener header/footer consistentes.
     *
     * @param string $view  Vista a renderizar.
     * @param array  $data  Datos para la vista.
     * @param string $layout Layout a usar (por defecto "layouts/main").
     */
    protected function renderWithLayout(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $data['contentView'] = $view;
        $this->render($layout, $data);
    }

    /**
     * Redirige a una URL interna del sistema.
     *
     * @param string $url  Ruta interna (ej: "usuarios/login").
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . APP_URL . '/' . $url);
        exit;
    }

    /**
     * Devuelve una respuesta JSON (para peticiones AJAX / APIs).
     *
     * @param mixed $data    Datos a convertir a JSON.
     * @param int   $status  Código HTTP de respuesta.
     */
    protected function jsonResponse(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Verifica si la petición actual es de tipo POST.
     *
     * @return bool
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Obtiene el valor de un campo del formulario (POST).
     *
     * @param string $key     Nombre del campo.
     * @param mixed  $default Valor por defecto si no existe.
     * @return mixed
     */
    protected function getPost(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtiene un valor de la URL (GET).
     *
     * @param string $key     Nombre del parámetro.
     * @param mixed  $default Valor por defecto.
     * @return mixed
     */
    protected function getGet(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
