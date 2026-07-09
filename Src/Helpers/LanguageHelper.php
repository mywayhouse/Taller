<?php
namespace App\Helpers;

use App\Models\Idioma;
use App\Models\Traduccion;

class LanguageHelper
{
    private static ?LanguageHelper $instance = null;
    private ?string $codigoActual = null;
    private array $diccionario = [];
    private array $idiomasDisponibles = [];

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    private function init(): void
    {
        $this->idiomasDisponibles = (new Idioma())->listarIdiomas();
        $codigoSesion = $_SESSION['lang_codigo'] ?? null;
        if ($codigoSesion && $this->idiomaValido($codigoSesion)) {
            $this->codigoActual = $codigoSesion;
        } else {
            $defecto = (new Idioma())->obtenerIdiomaDefecto();
            $this->codigoActual = $defecto['codigo'] ?? 'es';
        }
        $this->cargarDiccionario();
    }

    private function idiomaValido(string $codigo): bool
    {
        foreach ($this->idiomasDisponibles as $idioma) {
            if ($idioma['codigo'] === $codigo) return true;
        }
        return false;
    }

    private function cargarDiccionario(): void
    {
        $this->diccionario = (new Traduccion())->obtenerPorIdioma($this->codigoActual);
    }

    public static function setLanguage(string $codigo): bool
    {
        $instance = self::getInstance();
        if (!$instance->idiomaValido($codigo)) return false;
        $instance->codigoActual = $codigo;
        $_SESSION['lang_codigo'] = $codigo;
        $instance->cargarDiccionario();
        return true;
    }

    public static function getLanguage(): string
    {
        return self::getInstance()->codigoActual;
    }

    public static function getIdiomas(): array
    {
        return self::getInstance()->idiomasDisponibles;
    }

    public static function getIdiomaActual(): ?array
    {
        $instance = self::getInstance();
        foreach ($instance->idiomasDisponibles as $idioma) {
            if ($idioma['codigo'] === $instance->codigoActual) return $idioma;
        }
        return null;
    }

    public static function translate(string $clave, string $default = ''): string
    {
        $instance = self::getInstance();
        return $instance->diccionario[$clave] ?? ($default ?: $clave);
    }
}
