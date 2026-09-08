<?php

namespace App\Support;

class Cache
{
    private static string $dir = '';

    private static function dir(): string
    {
        if (!self::$dir) {
            self::$dir = storage_path('cache');
            if (!is_dir(self::$dir)) mkdir(self::$dir, 0700, true);
        }
        return self::$dir;
    }

    private static function key(string $key): string
    {
        return self::dir() . '/' . hash('sha256', $key) . '.cache';
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $file = self::key($key);
        if (!file_exists($file)) return $default;

        $data = unserialize(file_get_contents($file));
        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public static function put(string $key, mixed $value, int $ttl = 3600): void
    {
        $data = [
            'expires' => $ttl === 0 ? 0 : time() + $ttl,
            'value'   => $value,
        ];
        file_put_contents(self::key($key), serialize($data), LOCK_EX);
    }

    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = self::get($key);
        if ($cached !== null) return $cached;
        $value = $callback();
        self::put($key, $value, $ttl);
        return $value;
    }

    public static function forget(string $key): void
    {
        $file = self::key($key);
        if (file_exists($file)) unlink($file);
    }

    public static function flush(): void
    {
        foreach (glob(self::dir() . '/*.cache') as $file) {
            unlink($file);
        }
    }

    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }
}
