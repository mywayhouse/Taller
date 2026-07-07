<?php
// ============================================================
// database.php — Conexión a MySQL usando PDO (Singleton)
// ============================================================
// Proporciona una ÚNICA instancia de conexión PDO a lo largo
// de toda la ejecución del script (patrón Singleton).
// Esto evita abrir múltiples conexiones a la BD y mejora
// el rendimiento.
//
// REQUISITO: Trabajar exclusivamente con Stored Procedures.
// ============================================================

namespace config;

use PDO;
use PDOException;
use Exception;

class Database
{
    /**
     * Instancia única de la conexión PDO.
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    // ----------------------------------------------------------
    // Constructor privado: evita crear instancias desde fuera.
    // ----------------------------------------------------------
    private function __construct() {}

    // ----------------------------------------------------------
    // Clonación privada: evita clonar la instancia.
    // ----------------------------------------------------------
    private function __clone() {}

    /**
     * Obtiene la conexión PDO (la crea si no existe).
     * 
     * @return PDO
     * @throws Exception Si falla la conexión a la BD.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // --------------------------------------------------
            // Construir el DSN (Data Source Name) para MySQL
            // --------------------------------------------------
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );

            try {
                // --------------------------------------------------
                // Crear la conexión PDO con opciones seguras
                // --------------------------------------------------
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    // Lanzar excepciones en errores SQL (facilita debugging)
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    // Devolver resultados como array asociativo por defecto
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Deshabilitar emulación de prepared statements
                    // (usar los reales del driver MySQL = más seguro)
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);

                // --------------------------------------------------
                // Configurar la sesión de la conexión
                // --------------------------------------------------
                self::$instance->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                self::$instance->exec("SET time_zone = '-06:00'"); // UTC-6 (Honduras)
            } catch (PDOException $e) {
                // En producción, registrar el error en un log y mostrar
                // un mensaje genérico al usuario.
                error_log('Error de conexión BD: ' . $e->getMessage());
                throw new Exception('Error al conectar con la base de datos. Contacte al administrador.');
            }
        }

        return self::$instance;
    }

    /**
     * Ejecuta un Stored Procedure y devuelve el conjunto de
     * resultados (para consultas SELECT).
     *
     * @param string $procedure Nombre del SP (ej: "sp_listar_clientes")
     * @param array  $params    Parámetros asociativos [:param => valor]
     * @return array            Arreglo de registros (cada uno es un array asociativo)
     */
    public static function executeProcedure(string $procedure, array $params = []): array
    {
        $pdo = self::getConnection();
        $placeholders = !empty($params) ? implode(',', array_fill(0, count($params), '?')) : '';
        $sql = !empty($placeholders) ? "CALL {$procedure}({$placeholders})" : "CALL {$procedure}";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($params));
        // Si el SP devuelve filas, las capturamos.
        // Algunos SP pueden no devolver resultados (INSERT/UPDATE).
        $results = [];
        try {
            $results = $stmt->fetchAll();
        } catch (PDOException $e) {
            // El SP no devolvió resultados (no es un error)
        }

        $stmt->closeCursor();
        return $results;
    }

    /**
     * Ejecuta un Stored Procedure que NO devuelve filas
     * (INSERT, UPDATE, DELETE). Retorna el número de filas
     * afectadas.
     *
     * @param string $procedure Nombre del SP
     * @param array  $params    Parámetros [:param => valor]
     * @return int              Filas afectadas
     */
    public static function executeNonQuery(string $procedure, array $params = []): int
    {
        $pdo = self::getConnection();
        $placeholders = !empty($params) ? implode(',', array_fill(0, count($params), '?')) : '';
        $sql = !empty($placeholders) ? "CALL {$procedure}({$placeholders})" : "CALL {$procedure}";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($params));

        $affected = $stmt->rowCount();
        $stmt->closeCursor();

        return $affected;
    }
}
