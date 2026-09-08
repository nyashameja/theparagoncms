<?php

namespace App\Models;

use App\Support\Database;

abstract class BaseModel
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function find(int|string $id): ?array
    {
        $table = static::$table;
        $pk    = static::$primaryKey;
        return Database::selectOne("SELECT * FROM $table WHERE $pk = ? AND deleted_at IS NULL LIMIT 1", [$id]);
    }

    public static function findOrFail(int|string $id): array
    {
        $row = static::find($id);
        if (!$row) abort(404, 'Record not found');
        return $row;
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $table = static::$table;
        return Database::selectOne("SELECT * FROM $table WHERE $column = ? AND deleted_at IS NULL LIMIT 1", [$value]);
    }

    public static function all(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $table = static::$table;
        return Database::select("SELECT * FROM $table WHERE deleted_at IS NULL ORDER BY $orderBy $direction");
    }

    public static function where(string $column, mixed $value, string $orderBy = 'id'): array
    {
        $table = static::$table;
        return Database::select("SELECT * FROM $table WHERE $column = ? AND deleted_at IS NULL ORDER BY $orderBy", [$value]);
    }

    public static function paginate(int $page, int $perPage, string $where = '', array $params = [], string $orderBy = 'id DESC'): array
    {
        $table  = static::$table;
        $offset = ($page - 1) * $perPage;
        $whereClause = $where ? "WHERE $where AND deleted_at IS NULL" : 'WHERE deleted_at IS NULL';

        $total = Database::selectOne("SELECT COUNT(*) as count FROM $table $whereClause", $params)['count'] ?? 0;
        $rows  = Database::select("SELECT * FROM $table $whereClause ORDER BY $orderBy LIMIT $perPage OFFSET $offset", $params);

        return [
            'data'         => $rows,
            'total'        => (int) $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    public static function create(array $data): int|string
    {
        $data = static::addTimestamps($data, true);
        $table   = static::$table;
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        return Database::insert("INSERT INTO $table ($columns) VALUES ($placeholders)", array_values($data));
    }

    public static function update(int|string $id, array $data): int
    {
        $data  = static::addTimestamps($data, false);
        $table = static::$table;
        $pk    = static::$primaryKey;
        $set   = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        return Database::update("UPDATE $table SET $set WHERE $pk = ?", [...array_values($data), $id]);
    }

    public static function delete(int|string $id): int
    {
        $table = static::$table;
        $pk    = static::$primaryKey;
        return Database::update("UPDATE $table SET deleted_at = NOW() WHERE $pk = ?", [$id]);
    }

    public static function forceDelete(int|string $id): int
    {
        $table = static::$table;
        $pk    = static::$primaryKey;
        return Database::delete("DELETE FROM $table WHERE $pk = ?", [$id]);
    }

    public static function restore(int|string $id): int
    {
        $table = static::$table;
        $pk    = static::$primaryKey;
        return Database::update("UPDATE $table SET deleted_at = NULL WHERE $pk = ?", [$id]);
    }

    public static function count(string $where = '', array $params = []): int
    {
        $table = static::$table;
        $whereClause = $where ? "WHERE $where AND deleted_at IS NULL" : 'WHERE deleted_at IS NULL';
        return (int) (Database::selectOne("SELECT COUNT(*) as c FROM $table $whereClause", $params)['c'] ?? 0);
    }

    private static function addTimestamps(array $data, bool $isNew): array
    {
        $now = date('Y-m-d H:i:s');
        if ($isNew && !isset($data['created_at'])) {
            $data['created_at'] = $now;
        }
        $data['updated_at'] = $now;
        return $data;
    }
}
