<?php

function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false) return $default;

    return match (strtolower($value)) {
        'true', '(true)'   => true,
        'false', '(false)' => false,
        'null', '(null)'   => null,
        'empty', '(empty)' => '',
        default            => $value,
    };
}

function config(string $key, mixed $default = null): mixed
{
    static $cache = [];
    [$file, $rest] = array_pad(explode('.', $key, 2), 2, null);

    if (!isset($cache[$file])) {
        $path = dirname(__DIR__) . '/config/' . $file . '.php';
        $cache[$file] = file_exists($path) ? require $path : [];
    }

    if ($rest === null) return $cache[$file];

    $config = $cache[$file];
    foreach (explode('.', $rest) as $segment) {
        if (!is_array($config) || !array_key_exists($segment, $config)) return $default;
        $config = $config[$segment];
    }

    return $config;
}

function base_path(string $path = ''): string
{
    $base = dirname(__DIR__);
    return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
}

function public_path(string $path = ''): string
{
    $base = dirname(__DIR__) . '/public';
    return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
}

function storage_path(string $path = ''): string
{
    $base = dirname(__DIR__) . '/storage';
    return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
}

function resource_path(string $path = ''): string
{
    $base = dirname(__DIR__) . '/resources';
    return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
}

function view(string $template, array $data = []): string
{
    return \App\Support\View::render($template, $data);
}

function url(string $path = ''): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path = ''): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $url, int $status = 302): never
{
    header('Location: ' . $url, true, $status);
    exit;
}

function csrf_token(): string
{
    return \App\Support\Csrf::token();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function old(string $key, mixed $default = ''): mixed
{
    return \App\Support\Session::getFlash('_old.' . $key) ?? $default;
}

function session(string $key = null, mixed $default = null): mixed
{
    if ($key === null) return \App\Support\Session::all();
    return \App\Support\Session::get($key, $default);
}

function flash(string $key, mixed $value): void
{
    \App\Support\Session::flash($key, $value);
}

function now(): \DateTimeImmutable
{
    return new \DateTimeImmutable('now', new \DateTimeZone(config('app.timezone', 'Africa/Johannesburg')));
}

function str_slug(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\w\s-]/u', '', $text);
    $text = preg_replace('/[\s_]+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function abort(int $code, string $message = ''): never
{
    http_response_code($code);
    if (php_sapi_name() !== 'cli') {
        try {
            echo view('errors/' . $code, ['message' => $message]);
        } catch (\Throwable) {
            echo $message ?: 'Error ' . $code;
        }
    }
    exit;
}

function str_excerpt(string $text, int $length = 160): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

function reading_time(string $content): int
{
    $words = str_word_count(strip_tags($content));
    return max(1, (int) ceil($words / 200));
}

function format_bytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

function format_date(string|\DateTimeInterface $date, string $format = 'j F Y'): string
{
    if (is_string($date)) $date = new \DateTimeImmutable($date);
    return $date->format($format);
}

function zar(int|float $amount): string
{
    return 'R ' . number_format($amount, 2);
}

function setting(string $key, mixed $default = null): mixed
{
    static $cache = [];
    if (array_key_exists($key, $cache)) return $cache[$key];
    try {
        $row = \App\Support\Database::selectOne(
            "SELECT value FROM settings WHERE `key`=? LIMIT 1", [$key]
        );
        $cache[$key] = $row ? $row['value'] : $default;
    } catch (\Throwable) {
        $cache[$key] = $default;
    }
    return $cache[$key];
}
