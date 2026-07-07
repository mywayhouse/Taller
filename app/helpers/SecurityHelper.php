<?php
// ============================================================
// SecurityHelper.php — Funciones de seguridad reutilizables
// ============================================================
// Helper con métodos estáticos para sanitización, validación
// y protección contra vulnerabilidades web comunes.
// ============================================================

namespace app\helpers;

class SecurityHelper
{
    /**
     * Sanitiza un string para evitar XSS.
     *
     * @param string $input Texto a sanitizar.
     * @return string Texto seguro para imprimir en HTML.
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida que un correo tenga formato correcto.
     *
     * @param string $email Correo a validar.
     * @return bool True si es válido.
     */
    public static function validarEmail(string $email): bool
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Genera un hash seguro para contraseñas (bcrypt).
     *
     * @param string $password Contraseña en texto plano.
     * @return string Hash generado.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verifica una contraseña contra su hash.
     *
     * @param string $password Contraseña en texto plano.
     * @param string $hash     Hash almacenado.
     * @return bool True si coincide.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Genera un token CSRF y lo guarda en sesión.
     *
     * @return string Token generado.
     */
    public static function generarTokenCSRF(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * Verifica un token CSRF enviado desde un formulario.
     *
     * @param string $token Token recibido del formulario.
     * @return bool True si el token es válido.
     */
    public static function verificarTokenCSRF(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
