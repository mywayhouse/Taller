<?php
namespace Src\bd;

use PDO;
use PDOException;

class ConnectionDB
{
    private static string $host = '';
    private static string $user = '';
    private static string $pass = '';
    private static ?PDO $instance = null;

    public static function inicializar(string $host, string $user, string $password): void
    {
        self::$host = $host;
        self::$user = $user;
        self::$pass = $password;
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $opt = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
                $pdo = new PDO(self::$host, self::$user, self::$pass, $opt);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance = $pdo;
            } catch (PDOException $e) {
                error_log('Error al conectar a la base de datos: ' . $e->getMessage());
                die(json_encode([
                    'status' => 'ERROR',
                    'message' => 'Error al conectar con la base de datos. Contacte al administrador.',
                    'date' => date('Y-m-d H:i:s')
                ]));
            }
        }
        return self::$instance;
    }
}
