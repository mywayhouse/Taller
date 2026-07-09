<?php
namespace App\Core;

use App\bd\Database;
use PDO;

class Model
{
    protected ?PDO $db = null;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    protected function callProcedure(string $procedure, array $params = []): array
    {
        return Database::executeProcedure($procedure, $params);
    }

    protected function callNonQuery(string $procedure, array $params = []): int
    {
        return Database::executeNonQuery($procedure, $params);
    }
}
