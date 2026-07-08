<?php
// ============================================================
// Model.php — Modelo Base
// ============================================================
// Todos los modelos del sistema DEBEN extender esta clase.
// Proporciona acceso a la conexión PDO y métodos shorthand
// para ejecutar Stored Procedures sin repetir código.
//
// IMPORTANTE: Toda interacción con la BD debe hacerse a
// través de Stored Procedures, NO con consultas SQL
// escritas directamente en PHP.
// ============================================================

namespace App\core;

use Config\Database;
use PDO;

/**
 * Modelo base. Expone métodos protegidos para que los modelos
 * hijos ejecuten SP sin acoplamiento directo a la clase Database.
 */
class Model
{
    /**
     * Instancia de PDO (conexión única).
     * @var PDO|null
     */
    protected ?PDO $db = null;

    /**
     * Constructor: obtiene la conexión Singleton.
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Ejecuta un Stored Procedure que SÍ devuelve filas (SELECT).
     *
     * @param string $procedure Nombre del SP (ej: "sp_listar_clientes")
     * @param array  $params    Parámetros [:nombre => valor]
     * @return array            Arreglo de registros asociativos
     */
    protected function callProcedure(string $procedure, array $params = []): array
    {
        return Database::executeProcedure($procedure, $params);
    }

    /**
     * Ejecuta un Stored Procedure que NO devuelve filas
     * (INSERT/UPDATE/DELETE). Retorna filas afectadas.
     *
     * @param string $procedure Nombre del SP
     * @param array  $params    Parámetros
     * @return int              Filas afectadas
     */
    protected function callNonQuery(string $procedure, array $params = []): int
    {
        return Database::executeNonQuery($procedure, $params);
    }
}
