<?php
if (!function_exists('__')) {
    function __(string $clave, string $default = ''): string
    {
        return App\Helpers\LanguageHelper::translate($clave, $default);
    }
}
