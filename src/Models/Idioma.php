<?php
namespace App\Models;

use App\Core\Modelo;

class Idioma extends Modelo
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
