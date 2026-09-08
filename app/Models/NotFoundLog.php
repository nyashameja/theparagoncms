<?php

namespace App\Models;

use App\Support\Database;

class NotFoundLog extends BaseModel
{
    protected static string $table = 'not_found_logs';

    public static function record(string $path, string $fullUrl, string $referrer = '', string $ip = ''): void
    {
        try {
            $existing = Database::selectOne('SELECT id, hits FROM not_found_logs WHERE path = ? LIMIT 1', [$path]);
            if ($existing) {
                Database::update('UPDATE not_found_logs SET hits = hits + 1, last_seen_at = NOW() WHERE id = ?', [$existing['id']]);
            } else {
                Database::insert(
                    'INSERT INTO not_found_logs (path, full_url, referrer, ip_address, hits, last_seen_at, created_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())',
                    [$path, $fullUrl, $referrer, $ip]
                );
            }
        } catch (\Throwable) {}
    }
}
