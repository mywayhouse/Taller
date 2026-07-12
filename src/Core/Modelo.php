<?php
namespace App\Core;

use App\bd\BaseDatos;
use PDO;

class Modelo
{
    protected ?PDO $db = null;

    public function __construct()
    {
        $this->db = BaseDatos::getConnection();
    }

    protected function callProcedure(string $procedure, array $params = []): array
    {
        return BaseDatos::executeProcedure($procedure, $params);
    }

    protected function callNonQuery(string $procedure, array $params = []): int
    {
        return BaseDatos::executeNonQuery($procedure, $params);
    }
}