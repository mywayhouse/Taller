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

        // Evalúa si el arreglo de parámetros trae datos.
        // Si trae datos, extrae las llaves y arma el string "(:param1, :param2)".
        // Si viene vacío, simplemente asigna "()".
        $placeholders = empty($params) ? '()' : '(' . implode(', ', array_keys($params)) . ')';
        
        // Prepara la consulta concatenando el procedimiento y los paréntesis dinámicos
        $stmt = $pdo->prepare("CALL {$procedure}{$placeholders}");

        // Recorre el arreglo y enlaza de forma segura cada valor con su respectivo parámetro
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        // Ejecuta la consulta en la base de datos
        $stmt->execute();

        $results = [];
        try {
            // Captura todas las filas retornadas por el SELECT
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

        // Construcción dinámica de los paréntesis para la llamada
        $placeholders = empty($params) ? '()' : '(' . implode(', ', array_keys($params)) . ')';
        
        // Prepara la llamada evitando inyecciones de código
        $stmt = $pdo->prepare("CALL {$procedure}{$placeholders}");

        // Vincula los parámetros reales a los placeholders de la consulta
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        $stmt->execute();

        // Cuenta cuántos registros fueron afectados (creados, actualizados o borrados lógicamente)
        $affected = $stmt->rowCount();
        
        $stmt->closeCursor();

        return $affected;
    }
}
