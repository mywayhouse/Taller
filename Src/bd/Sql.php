<?php
namespace Src\bd;

class Sql extends ConnectionDB
{
    public static function verificarRegistro(string $sql, string $condicion, mixed $params): bool
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare($sql);
            $query->execute([$condicion => $params]);
            return $query->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Sql::verificarRegistro -> " . $e->getMessage());
            return false;
        }
    }
}
