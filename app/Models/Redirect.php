<?php

namespace App\Models;

use App\Support\Database;

class Redirect extends BaseModel
{
    protected static string $table = 'redirects';

    public static function findBySource(string $path): ?array
    {
        return Database::selectOne(
            'SELECT * FROM redirects WHERE source_path = ? AND is_active = 1 AND deleted_at IS NULL LIMIT 1',
            [rtrim($path, '/')]
        );
    }
}
