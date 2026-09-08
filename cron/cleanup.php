<?php
/**
 * Cleanup cron job.
 * Schedule: 0 2 * * * (daily at 02:00)
 * Run: php /path/to/cron/cleanup.php
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/bootstrap/app.php';

use App\Support\Database;
use App\Support\Logger;

$log = fn(string $msg) => Logger::info('[cron/cleanup] ' . $msg);

$log('Starting cleanup run');

/* ── Purge soft-deleted records older than 30 days ─────── */
$tables = ['leads','articles','services','projects','media','redirects','testimonials','case_studies','pages','menus','menu_items','users'];
foreach ($tables as $table) {
    $n = Database::update(
        "DELETE FROM `{$table}` WHERE deleted_at IS NOT NULL AND deleted_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );
    if ($n) $log("Purged {$n} from {$table}");
}

/* ── Clear expired password reset tokens ───────────────── */
$n = Database::update("DELETE FROM password_resets WHERE expires_at < NOW()");
if ($n) $log("Purged {$n} expired password reset tokens");

/* ── Clear expired sessions ─────────────────────────────── */
$n = Database::update("DELETE FROM sessions WHERE last_activity < (UNIX_TIMESTAMP() - 7200)");
if ($n) $log("Purged {$n} expired sessions");

/* ── Truncate old analytics events (keep 12 months) ────── */
$n = Database::update(
    "DELETE FROM analytics_events WHERE created_at < DATE_SUB(NOW(), INTERVAL 12 MONTH)"
);
if ($n) $log("Pruned {$n} analytics events older than 12 months");

/* ── Truncate old audit log entries (keep 6 months) ────── */
$n = Database::update(
    "DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)"
);
if ($n) $log("Pruned {$n} audit log entries older than 6 months");

/* ── Prune old 404 log entries (keep 3 months) ─────────── */
$n = Database::update(
    "DELETE FROM not_found_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH)"
);
if ($n) $log("Pruned {$n} 404 log entries older than 3 months");

/* ── Clear stale file cache ─────────────────────────────── */
$cacheDir = BASE_PATH . '/storage/cache';
if (is_dir($cacheDir)) {
    $count = 0;
    foreach (glob($cacheDir . '/*.cache') ?: [] as $file) {
        $data = @unserialize(@file_get_contents($file));
        if ($data === false || (isset($data['expires']) && $data['expires'] < time())) {
            unlink($file);
            $count++;
        }
    }
    if ($count) $log("Cleared {$count} expired cache files");
}

$log('Cleanup run complete');
