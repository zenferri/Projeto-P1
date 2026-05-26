<?php

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            // Load optional config file if present
            $config = [];
            $configFile = __DIR__ . '/config.php';
            if (file_exists($configFile)) {
                $loaded = require $configFile;
                if (is_array($loaded)) {
                    $config = $loaded;
                }
            }

            $host = getenv('DB_HOST') ?: ($config['DB_HOST'] ?? '127.0.0.1');
            $port = getenv('DB_PORT') ?: ($config['DB_PORT'] ?? '3306');
            $dbName = getenv('DB_NAME') ?: ($config['DB_NAME'] ?? 'singularys');
            $username = getenv('DB_USER') ?: ($config['DB_USER'] ?? 'root');
            $password = getenv('DB_PASS') ?: ($config['DB_PASS'] ?? '');

            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                $connection = new PDO($dsn, $username, $password, $options);
                $connection->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $connection->exec("USE `{$dbName}`;");
                self::$instance = $connection;
            } catch (PDOException $e) {
                $hint = "Verifique as credenciais em config/config.php ou as variáveis de ambiente DB_HOST, DB_USER, DB_PASS, DB_NAME. \n" .
                        "No XAMPP (Windows) normalmente o usuário padrão é 'root' com senha vazia ou você pode criar um usuário via phpMyAdmin.\n" .
                        "Exemplo SQL:\nCREATE DATABASE singularys;\nCREATE USER 'singularys_user'@'localhost' IDENTIFIED BY 'SENHA';\nGRANT ALL PRIVILEGES ON singularys.* TO 'singularys_user'@'localhost';\nFLUSH PRIVILEGES;";

                throw new PDOException($e->getMessage() . ' — ' . $hint, (int)$e->getCode());
            }
        }

        return self::$instance;
    }
}
