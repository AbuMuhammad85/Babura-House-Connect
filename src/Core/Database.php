<?php

namespace App\Core;

use App\Core\Config;

class Database
{
    private static ?\PDO $connection = null;

    /**
     * Get the active PDO database connection instance.
     */
    public static function getConnection(): \PDO
    {
        if (self::$connection === null) {
            $host = Config::get('database.host', '127.0.0.1');
            $port = Config::get('database.port', '3306');
            $dbName = Config::get('database.database', 'babura_house_connect');
            $user = Config::get('database.username', 'root');
            $pass = Config::get('database.password', '');
            $options = Config::get('database.options', []);

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

            try {
                self::$connection = new \PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                throw new \RuntimeException("Database connection failed: Please verify your local MySQL server is running.");
            }
        }

        return self::$connection;
    }

    /**
     * Execute a prepared statement query and return the statement.
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all records matching the query parameters.
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Fetch a single record matching the query parameters.
     */
    public static function fetch(string $sql, array $params = [])
    {
        return self::query($sql, $params)->fetch();
    }

    /**
     * Begin database transaction.
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit database transaction.
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Rollback database transaction.
     */
    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Get the last inserted ID.
     */
    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }
}
