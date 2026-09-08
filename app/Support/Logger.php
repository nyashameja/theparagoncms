<?php

namespace App\Support;

class Logger
{
    public static function log(string $level, string $message, array $context = []): void
    {
        $logFile = storage_path('logs/' . date('Y-m-d') . '.log');
        $dir = dirname($logFile);
        if (!is_dir($dir)) mkdir($dir, 0700, true);

        // Redact sensitive keys
        $redactKeys = ['password', 'password_confirmation', 'token', 'secret', 'key', 'card', 'cvv'];
        array_walk_recursive($context, function (&$value, $key) use ($redactKeys) {
            foreach ($redactKeys as $redact) {
                if (str_contains(strtolower((string)$key), $redact)) {
                    $value = '[REDACTED]';
                    return;
                }
            }
        });

        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            empty($context) ? '' : json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        error_log($line, 3, $logFile);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log('critical', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('warning', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        if (env('APP_DEBUG', false)) {
            self::log('debug', $message, $context);
        }
    }

    public static function audit(string $event, array $context = []): void
    {
        $logFile = storage_path('logs/audit-' . date('Y-m') . '.log');
        $userId = \App\Support\Session::get('user_id', 'guest');
        $ip     = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $line = sprintf(
            "[%s] AUDIT user=%s ip=%s event=%s %s\n",
            date('Y-m-d H:i:s'), $userId, $ip, $event,
            empty($context) ? '' : json_encode($context, JSON_UNESCAPED_UNICODE)
        );

        error_log($line, 3, $logFile);

        // Also persist to database audit log if available
        try {
            Database::query(
                'INSERT INTO audit_logs (user_id, event, ip_address, data, created_at)
                 VALUES (?, ?, ?, ?, NOW())',
                [$userId === 'guest' ? null : $userId, $event, $ip, json_encode($context)]
            );
        } catch (\Throwable) {
            // DB not available yet during install
        }
    }
}
