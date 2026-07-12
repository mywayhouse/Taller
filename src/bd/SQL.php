<?php
namespace App\bd;

class SQL extends ConexionBD
{
    public static function verificarRegistro(string $sql, string $condicion, mixed $params): bool
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare($sql);
            $query->execute([$condicion => $params]);
            return $query->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("SQL::verificarRegistro -> " . $e->getMessage());
            return false;
        }
    }
}