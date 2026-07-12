<?php
namespace Src\Config;

class LogsErrores
{
    public static function activar(): void
    {
        error_reporting(E_ALL);
        ini_set('ignore_repeated_errors', 1);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        ini_set('error_log', dirname(__DIR__) . '/Logs/php-error.log');
    }
}
