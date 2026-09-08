<?php

namespace App\Support;

class RateLimit
{
    public static function check(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $cacheKey = 'rate_limit:' . hash('sha256', $key);
        $data     = Cache::get($cacheKey, ['attempts' => 0, 'reset_at' => time() + $decaySeconds]);

        if (time() > $data['reset_at']) {
            $data = ['attempts' => 0, 'reset_at' => time() + $decaySeconds];
        }

        $data['attempts']++;
        Cache::put($cacheKey, $data, $decaySeconds + 10);

        return $data['attempts'] <= $maxAttempts;
    }

    public static function attempts(string $key, int $decaySeconds): int
    {
        $cacheKey = 'rate_limit:' . hash('sha256', $key);
        $data     = Cache::get($cacheKey, ['attempts' => 0, 'reset_at' => 0]);
        if (time() > $data['reset_at']) return 0;
        return $data['attempts'];
    }

    public static function clear(string $key): void
    {
        Cache::forget('rate_limit:' . hash('sha256', $key));
    }

    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        return !self::check($key, $maxAttempts, $decaySeconds);
    }
}
