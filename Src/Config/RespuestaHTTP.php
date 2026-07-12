<?php
namespace Src\Config;

class RespuestaHTTP
{
    private static array $mensaje = [
        'status' => '',
        'message' => '',
        'data' => '',
        'date' => ''
    ];

    public static function status200(mixed $res): array
    {
        http_response_code(200);
        self::$mensaje['status'] = 'OK';
        self::$mensaje['message'] = $res;
        self::$mensaje['date'] = date('Y-m-d H:i:s');
        return self::$mensaje;
    }

    public static function status201(): array
    {
        http_response_code(201);
        self::$mensaje['status'] = 'OK';
        self::$mensaje['message'] = 'Recurso creado exitosamente!';
        self::$mensaje['date'] = date('Y-m-d H:i:s');
        return self::$mensaje;
    }

    public static function status400(string $res): array
    {
        http_response_code(400);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = $res;
        self::$mensaje['date'] = date('Y-m-d H:i:s');
        return self::$mensaje;
    }

    public static function status401(string $str = ''): array
    {
        http_response_code(401);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = 'No tiene privilegios para acceder al recurso! ' . $str;
        self::$mensaje['date'] = date('Y-m-d H:i:s');
        return self::$mensaje;
    }

    public static function status404(string $res = ''): array
    {
        http_response_code(404);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = $res ?: 'No existe el recurso solicitado!';
        self::$mensaje['date'] = date('Y-m-d H:i:s');
        return self::$mensaje;
    }

    public static function status500(): array
    {
        http_response_code(500);
        self::$mensaje['status'] = 'ERROR';
        self::$mensaje['message'] = 'Se ha producido un error en el servidor!';
        self::$mensaje['date'] = date('Y-m-d H:i:s');
        return self::$mensaje;
    }
}
