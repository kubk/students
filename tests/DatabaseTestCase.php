<?php

declare(strict_types=1);

namespace Tests;

// https://phpunit.de/manual/current/en/database.html
// Tip: Use your own Abstract Database TestCase
abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    static protected $pdo = null;
    protected $connection = null;

    protected function getConnection()
    {
        if ($this->connection === null) {
            if (!self::$pdo) {
                $config = require __DIR__ . '/../config/config_tests.php';
                $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
                self::$pdo = new \PDO($dsn, $config['username'], $config['password']);
            }
            $this->connection = $this->createDefaultDBConnection(self::$pdo);
            $this->createSchema(self::$pdo);
        }
        return $this->connection;
    }

    private function createSchema(\PDO $pdo)
    {
        $pdo->exec(file_get_contents(__DIR__ . '/../create-students-table.sql'));
    }
}