<?php

declare(strict_types=1);

// Redirect to installer if not installed
if (!file_exists(dirname(__DIR__) . '/.env') || getenv('INSTALLER_LOCK') !== 'true') {
    $lockFile = dirname(__DIR__) . '/storage/installed.lock';
    if (!file_exists($lockFile) && !str_contains($_SERVER['REQUEST_URI'] ?? '', '/installer')) {
        header('Location: /installer');
        exit;
    }
}

$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

// Check maintenance mode
$settings = \App\Models\Setting::all();
if (!empty($settings['maintenance_mode']) && $settings['maintenance_mode'] === '1') {
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    if (!str_starts_with($path, '/admin')) {
        http_response_code(503);
        echo \App\Support\View::render('errors/maintenance', [
            'title'   => 'Maintenance Mode | The Paragon .Design',
            'message' => $settings['maintenance_message'] ?? 'We\'ll be back shortly.',
        ]);
        exit;
    }
}

// Share global data with views
\App\Support\View::share('brand', \App\Models\Setting::getBrand());
\App\Support\View::share('contact', \App\Models\Setting::getContact());
\App\Support\View::share('settings', $settings);
\App\Support\View::share('nav', \App\Models\Menu::getByLocation('primary'));
\App\Support\View::share('footerNav', \App\Models\Menu::getByLocation('footer'));

// Track pageview analytics
if (!headers_sent()) {
    $trackPath = $_SERVER['REQUEST_URI'] ?? '/';
    try {
        \App\Support\Database::query(
            'INSERT INTO analytics_events (event_type, path, ip_address, user_agent, referer, created_at) VALUES (?, ?, ?, ?, ?, NOW())',
            ['pageview', parse_url($trackPath, PHP_URL_PATH), $_SERVER['REMOTE_ADDR'] ?? '', substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255), substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500)]
        );
    } catch (\Throwable) {}
}

$app->run();
