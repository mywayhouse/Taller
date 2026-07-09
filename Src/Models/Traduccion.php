<?php
namespace App\Models;

use App\Core\Model;

class Traduccion extends Model
{
    public function obtenerPorIdioma(string $codigoIdioma): array
    {
        $rows = $this->callProcedure('sp_obtener_traducciones', [
            ':p_codigo_idioma' => $codigoIdioma
        ]);
        $diccionario = [];
        foreach ($rows as $row) {
            $diccionario[$row['clave_etiqueta']] = $row['texto'];
        }
        return $diccionario;
    }

    public function obtenerTodas(): array
    {
        $idiomaModel = new Idioma();
        $idiomas = $idiomaModel->listarIdiomas();
        $todas = [];
        foreach ($idiomas as $idioma) {
            $todas[$idioma['codigo']] = $this->obtenerPorIdioma($idioma['codigo']);
        }
        return $todas;
    }
}
