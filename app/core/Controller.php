<?php
// ============================================================
// Controller.php — Controlador Base
// ============================================================
// Todos los controladores del sistema DEBEN extender esta
// clase. Proporciona métodos utilitarios comunes como:
//   - render()            -> Cargar una vista con datos.
//   - redirect()          -> Redirigir a otra URL.
//   - jsonResponse()      -> Devolver JSON (para peticiones AJAX).
//   - isPost()            -> Verificar si la petición es POST.
//   - requireAccess()     -> Validar permiso de acceso a módulo.
//   - requireWriteAccess()-> Validar permiso de escritura.
//   - requireAuth()       -> Validar sesión activa.
//   - audit()             -> Registrar evento en logs_sistema.
// ============================================================
namespace App\core;
use App\helpers\AccessHelper;
use App\helpers\AuditHelper;
use App\helpers\LanguageHelper;

/**
 * Clase base para todos los Controladores de la aplicación.
 */
class Controller
{
    /**
     * Traduce una clave de etiqueta al idioma actual.
     *
     * @param string $clave   Clave de la etiqueta (ej: "welcome_msg").
     * @param string $default Texto por defecto si no se encuentra la clave.
     * @return string
     */
    protected function __(string $clave, string $default = ''): string
    {
        return LanguageHelper::translate($clave, $default);
    }

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

    // ==========================================================
    // MÉTODOS DE SEGURIDAD Y AUDITORÍA
    // ==========================================================

    /**
     * Verifica que el usuario tenga sesión activa.
     * Si no, redirige al login.
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth/login');
        }
    }

    /**
     * Verifica que el rol del usuario tenga acceso al módulo.
     * Si no, redirige al dashboard con mensaje de error.
     *
     * @param string $module Nombre del módulo (ej: "clientes", "usuarios").
     */
    protected function requireAccess(string $module): void
    {
        $this->requireAuth();
        AccessHelper::requireAccess($module);
    }

    /**
     * Verifica que el rol del usuario tenga permiso de escritura
     * en el módulo (crear, editar, eliminar).
     *
     * @param string $module Nombre del módulo.
     */
    protected function requireWriteAccess(string $module): void
    {
        $this->requireAuth();
        AccessHelper::requireWriteAccess($module);
    }

    /**
     * Verifica si el usuario tiene acceso de lectura al módulo.
     *
     * @param string $module Nombre del módulo.
     * @return bool
     */
    protected function checkAccess(string $module): bool
    {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }
        return AccessHelper::hasAccess($module);
    }

    /**
     * Registra un evento en la tabla logs_sistema.
     * Usa el usuario actual de la sesión automáticamente.
     *
     * @param string $accion Descripción de la acción realizada.
     */
    protected function audit(string $accion): void
    {
        AuditHelper::logCurrentUser($accion);
    }
}
