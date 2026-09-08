<?php

namespace App\Support;

class Database
{
    private static ?\PDO $connection = null;

    public static function connection(): \PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $driver = config('database.default', 'mysql');
        $cfg    = config("database.connections.$driver");

        if ($driver === 'sqlite') {
            $dsn = 'sqlite:' . $cfg['database'];
            self::$connection = new \PDO($dsn, null, null, $cfg['options'] ?? []);
            self::$connection->exec('PRAGMA foreign_keys = ON;');
        } else {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']
            );
            self::$connection = new \PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options'] ?? []);
        }

        return self::$connection;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function select(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function selectOne(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function insert(string $sql, array $params = []): int|string
    {
        self::query($sql, $params);
        return self::connection()->lastInsertId();
    }

    public static function update(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function delete(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function statement(string $sql): bool
    {
        return self::connection()->exec($sql) !== false;
    }

    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connection();
        $pdo->beginTransaction();
        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function tableExists(string $table): bool
    {
        try {
            $driver = config('database.default', 'mysql');
            if ($driver === 'sqlite') {
                $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=?";
            } else {
                $sql = "SHOW TABLES LIKE ?";
            }
            $result = self::selectOne($sql, [$table]);
            return $result !== null;
        } catch (\Throwable) {
            return false;
        }
    }
}
