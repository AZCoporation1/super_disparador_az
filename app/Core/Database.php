<?php
namespace App\Core;

/**
 * Database Connection Singleton (PDO)
 */
class Database
{
    private static ?\PDO $instance = null;

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $config = CONFIG['database'];
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";

            try {
                self::$instance = new \PDO($dsn, $config['user'], $config['pass'], [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (\PDOException $e) {
                if (CONFIG['app']['env'] === 'development') {
                    die("Erro de conexão com o banco: " . $e->getMessage());
                }
                die("Erro interno. Tente novamente mais tarde.");
            }
        }

        return self::$instance;
    }

    // Prevent cloning and unserialization
    private function __construct()
    {
    }
    private function __clone()
    {
    }
}
