<?php
namespace App\Helpers;

class DateHelper
{
    public static function formatDate(?string $date): string
    {
        if (empty($date) || $date === '0000-00-00') {
            return '-';
        }
        return date('d/m/Y', strtotime($date));
    }

    public static function formatDateTime(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }
        return date('d/m/Y H:i', strtotime($datetime));
    }

    public static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
