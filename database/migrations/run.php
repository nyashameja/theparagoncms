<?php

/**
 * Migration runner — execute from CLI or installer.
 * Usage: php database/migrations/run.php
 */

require_once dirname(__DIR__, 2) . '/bootstrap/app.php';

use App\Support\Database;
use App\Support\Logger;

$migrationFiles = glob(__DIR__ . '/*.sql');
sort($migrationFiles);

// Track executed migrations
try {
    Database::statement(
        "CREATE TABLE IF NOT EXISTS migrations (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            filename   VARCHAR(255) NOT NULL UNIQUE,
            executed_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
} catch (\Throwable $e) {
    // SQLite compatible
    Database::statement(
        "CREATE TABLE IF NOT EXISTS migrations (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            filename    TEXT NOT NULL UNIQUE,
            executed_at TEXT NOT NULL
        )"
    );
}

$executed = array_column(
    Database::select('SELECT filename FROM migrations'),
    'filename'
);

$ran = 0;
foreach ($migrationFiles as $file) {
    $filename = basename($file);
    if (in_array($filename, $executed)) continue;

    echo "Running migration: $filename\n";

    $sql = file_get_contents($file);
    // Split by semicolon (handle multi-statement files)
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    try {
        foreach ($statements as $stmt) {
            if (empty($stmt) || str_starts_with($stmt, '--')) continue;
            Database::statement($stmt);
        }

        Database::insert(
            'INSERT INTO migrations (filename, executed_at) VALUES (?, ?)',
            [$filename, date('Y-m-d H:i:s')]
        );
        $ran++;
        echo "  ✓ Done\n";
    } catch (\Throwable $e) {
        echo "  ✗ Failed: " . $e->getMessage() . "\n";
        Logger::error("Migration failed: $filename", ['error' => $e->getMessage()]);
    }
}

echo "\n$ran migration(s) executed.\n";
