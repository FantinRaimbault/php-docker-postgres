<?php

class Db
{
    private static $_instance; // L'attribut qui stockera l'instance unique

    private static string $host = 'postgres';
    private static string $port = '5432';
    private static string $dbName = 'toto';
    private static string $user = 'postgres';
    private static string $password = 'postgres';

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            try {
                $pdo = new PDO(
                    'pgsql:host=' . self::$host .
                    ';port=' . self::$port .
                    ';dbname=' . self::$dbName .
                    ';user=' . self::$user .
                    ';password=' . self::$password
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                self::$_instance = $pdo;
            } catch (\Throwable $th) {
                die('connection failed ' . $th);
            }
        }
        return self::$_instance;
    }

    private function __construct()
    {
    }
}

Db::getInstance();
