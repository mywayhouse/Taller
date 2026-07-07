<?php
// ============================================================
// DateHelper.php — Funciones auxiliares para fechas
// ============================================================
// Formateo consistente de fechas y horas en toda la app.
// ============================================================

namespace App\helpers;

class DateHelper
{
    /**
     * Formatea una fecha al formato d/m/Y (ej: 15/03/2026).
     *
     * @param string|null $date Fecha en formato MySQL (Y-m-d) o null.
     * @return string Fecha formateada o "-" si es nula.
     */
    public static function formatDate(?string $date): string
    {
        if (empty($date) || $date === '0000-00-00') {
            return '-';
        }
        return date('d/m/Y', strtotime($date));
    }

    /**
     * Formatea una fecha con hora (d/m/Y H:i).
     *
     * @param string|null $datetime Fecha-hora en formato MySQL.
     * @return string Fecha con hora formateada.
     */
    public static function formatDateTime(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }
        return date('d/m/Y H:i', strtotime($datetime));
    }

    /**
     * Retorna la fecha actual en formato MySQL (Y-m-d H:i:s).
     *
     * @return string
     */
    public static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
