<?php
namespace App\models;

use App\core\Model;

class Idioma extends Model
{
    public function listarIdiomas(): array
    {
        return $this->callProcedure('sp_listar_idiomas');
    }

    public function obtenerIdiomaDefecto(): ?array
    {
        $rows = $this->callProcedure('sp_listar_idiomas');
        foreach ($rows as $row) {
            if ($row['defecto'] == 1) return $row;
        }
        return $rows[0] ?? null;
    }

    public function obtenerPorCodigo(string $codigo): ?array
    {
        $rows = $this->callProcedure('sp_listar_idiomas');
        foreach ($rows as $row) {
            if ($row['codigo'] === $codigo) return $row;
        }
        return null;
    }
}
