<?php
namespace Src\bd;

use Dotenv\Dotenv;

class DataDB
{
    public static array $data = [];

    public static function load(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
        $dotenv->load();

        self::$data = [
            'IP' => $_ENV['IP'],
            'user' => $_ENV['USER'],
            'password' => $_ENV['PASSWORD'],
            'DB' => $_ENV['DB'],
            'port' => $_ENV['PORT']
        ];

        $host = 'mysql:host=' . self::$data['IP'] . ';port=' . self::$data['port'] . ';dbname=' . self::$data['DB'];

        ConnectionDB::inicializar($host, self::$data['user'], self::$data['password']);
    }
}
