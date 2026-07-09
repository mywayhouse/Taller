<?php
namespace App\bd;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    private static function buildCallSql(string $procedure, array $params): string
    {
        if (empty($params)) {
            return "CALL $procedure";
        }
        $keys = array_keys($params);
        $placeholders = array_map(function ($key) {
            return is_string($key) ? $key : '?';
        }, $keys);
        return "CALL $procedure(" . implode(', ', $placeholders) . ")";
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                self::$instance->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                self::$instance->exec("SET time_zone = '-06:00'");
            } catch (PDOException $e) {
                error_log('Error de conexion BD: ' . $e->getMessage());
                throw new Exception('Error al conectar con la base de datos. Contacte al administrador.');
            }
        }
        return self::$instance;
    }

    public static function executeProcedure(string $procedure, array $params = []): array
    {
        $pdo = self::getConnection();
        $sql = self::buildCallSql($procedure, $params);
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        $results = [];
        try {
            $results = $stmt->fetchAll();
        } catch (PDOException $e) {}
        $stmt->closeCursor();
        return $results;
    }

    public static function executeNonQuery(string $procedure, array $params = []): int
    {
        $pdo = self::getConnection();
        $sql = self::buildCallSql($procedure, $params);
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        $affected = $stmt->rowCount();
        $stmt->closeCursor();
        return $affected;
    }
}
