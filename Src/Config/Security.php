<?php
namespace Src\Config;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Security
{
    private static $jwt_data;

    public static function secretKey(): string
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
        $dotenv->load();
        return $_ENV['SECRET_KEY'];
    }

    public static function createPassword(string $pass): string
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    public static function validatePassword(string $pw, string $pwh): bool
    {
        return password_verify($pw, $pwh);
    }

    public static function createTokenJwt(string $key, array $data): string
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60 * 6),
            "data" => $data
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function validateTokenJwt(string $token, string $key): object
    {
        if (!isset(getallheaders()['Authorization'])) {
            die(json_encode(ResponseHTTP::status400("Para proceder el token de acceso es requerido!")));
        }
        try {
            $jwt = explode(" ", getallheaders()['Authorization']);
            $data = JWT::decode($jwt[1], new Key($key, 'HS256'));
            self::$jwt_data = $data;
            return self::$jwt_data;
        } catch (\Exception $e) {
            error_log('Token invalido o expiro: ' . $e->getMessage());
            die(json_encode(ResponseHTTP::status401('Token invalido o ha expirado')));
        }
    }

    public static function getDataJwt(): array
    {
        $jwt_decoded_array = json_decode(json_encode(self::$jwt_data), true);
        return $jwt_decoded_array['data'];
    }
}
