<?php

declare(strict_types=1);
namespace TurtleShortener\Database;

use PDO;

class DbUtil {
    private static ?PDO $pdo = null;

    public static function getPdo(): PDO {
        if (self::$pdo === null) {
            $settings = $GLOBALS['settings'];
            $db_host = $settings['db_host'];
            $db_name = $settings['db_name'];
            $db_user = $settings['db_user'];
            $db_pass = $settings['db_pass'];
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";
            self::$pdo = new PDO($dsn, $db_user, $db_pass);
        }
        return self::$pdo;
    }
}
