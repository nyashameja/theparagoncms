<?php

define('PARAGON_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));

// Load environment
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, '"\'');
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

require_once __DIR__ . '/helpers.php';

// Autoloader
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\'       => dirname(__DIR__) . '/app/',
        'Database\\'  => dirname(__DIR__) . '/database/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) continue;
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = $baseDir . $relative . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Error handling
if (env('APP_DEBUG', false)) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    set_error_handler(function ($errno, $errstr, $errfile, $errline): bool {
        if (!(error_reporting() & $errno)) return false;
        \App\Support\Logger::error("PHP Error [$errno]: $errstr in $errfile:$errline");
        return true;
    });
    set_exception_handler(function (\Throwable $e): void {
        \App\Support\Logger::critical($e->getMessage(), [
            'file' => $e->getFile(), 'line' => $e->getLine(),
        ]);
        if (!headers_sent()) {
            http_response_code(500);
        }
        try {
            echo view('errors/500', ['message' => 'An unexpected error occurred.']);
        } catch (\Throwable) {
            echo '<h1>500 Internal Server Error</h1>';
        }
    });
}

// Session
\App\Support\Session::start();

// Timezone
date_default_timezone_set(config('app.timezone', 'Africa/Johannesburg'));

// Security headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

return new \App\Support\Application();
