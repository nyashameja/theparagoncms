<?php

namespace App\Support;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $cfg = config('session');
        $savePath = $cfg['save_path'] ?? storage_path('sessions');

        if (!is_dir($savePath)) {
            mkdir($savePath, 0700, true);
        }

        session_save_path($savePath);
        session_name('paragon_sess');

        $isSecure = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 80) == 443)
            && ($cfg['secure'] ?? true);

        session_set_cookie_params([
            'lifetime' => ($cfg['lifetime'] ?? 120) * 60,
            'path'     => $cfg['path'] ?? '/',
            'domain'   => $cfg['domain'] ?? '',
            'secure'   => $isSecure,
            'httponly' => $cfg['httponly'] ?? true,
            'samesite' => $cfg['samesite'] ?? 'Strict',
        ]);

        session_start();
        self::$started = true;

        // Regenerate periodically
        if (!isset($_SESSION['_last_regenerated'])) {
            session_regenerate_id(true);
            $_SESSION['_last_regenerated'] = time();
        } elseif (time() - $_SESSION['_last_regenerated'] > 300) {
            session_regenerate_id(true);
            $_SESSION['_last_regenerated'] = time();
        }

        // Process flash
        if (isset($_SESSION['_flash_next'])) {
            $_SESSION['_flash'] = $_SESSION['_flash_next'];
            unset($_SESSION['_flash_next']);
        } else {
            $_SESSION['_flash'] = [];
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function all(): array
    {
        return $_SESSION;
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash_next'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function reflash(): void
    {
        $_SESSION['_flash_next'] = array_merge(
            $_SESSION['_flash_next'] ?? [],
            $_SESSION['_flash'] ?? []
        );
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
        $_SESSION['_last_regenerated'] = time();
    }
}
