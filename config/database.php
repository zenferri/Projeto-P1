<?php

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $port = '3306';
            $dbName = 'singularys';
            $username = 'root';
            $password = '';
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $connection = new PDO($dsn, $username, $password, $options);
            $connection->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $connection->exec("USE `{$dbName}`;");
            self::$instance = $connection;
        }

        return self::$instance;
    }
}
