<?php

use App\Support\Router;

/** @var Router $router */

// ─── Public Routes ──────────────────────────────────────────────────────────

$router->get('/', [\App\Controllers\Public\HomeController::class, 'index']);
$router->get('/about', [\App\Controllers\Public\AboutController::class, 'index']);

// Contact
$router->get('/contact', [\App\Controllers\Public\FormsController::class, 'contact']);
$router->post('/contact', [\App\Controllers\Public\FormsController::class, 'submitContact'], ['csrf']);

// Services
$router->get('/services', [\App\Controllers\Public\ServicesController::class, 'index']);
$router->get('/services/{slug}', [\App\Controllers\Public\ServicesController::class, 'show']);

// Portfolio
$router->get('/portfolio', [\App\Controllers\Public\PortfolioController::class, 'index']);
$router->get('/portfolio/{slug}', [\App\Controllers\Public\PortfolioController::class, 'show']);

// Case Studies
$router->get('/case-studies', [\App\Controllers\Public\CaseStudyController::class, 'index']);
$router->get('/case-studies/{slug}', [\App\Controllers\Public\CaseStudyController::class, 'show']);

// Industries
$router->get('/industries/{slug}', [\App\Controllers\Public\IndustryController::class, 'show']);

// Locations
$router->get('/locations/{slug}', [\App\Controllers\Public\LocationController::class, 'show']);

// Blog
$router->get('/blog', [\App\Controllers\Public\BlogController::class, 'index']);
$router->get('/blog/category/{slug}', [\App\Controllers\Public\BlogController::class, 'category']);
$router->get('/blog/tag/{slug}', [\App\Controllers\Public\BlogController::class, 'tag']);
$router->get('/blog/{slug}', [\App\Controllers\Public\BlogController::class, 'show']);

// Packages
$router->get('/packages', [\App\Controllers\Public\PackagesController::class, 'index']);

// Lead forms
$router->get('/get-quote', [\App\Controllers\Public\FormsController::class, 'quote']);
$router->post('/get-quote', [\App\Controllers\Public\FormsController::class, 'submitQuote'], ['csrf']);
$router->get('/book-consultation', [\App\Controllers\Public\FormsController::class, 'consultation']);
$router->post('/book-consultation', [\App\Controllers\Public\FormsController::class, 'submitConsultation'], ['csrf']);
$router->get('/free-audit', [\App\Controllers\Public\FormsController::class, 'audit']);
$router->post('/free-audit', [\App\Controllers\Public\FormsController::class, 'submitAudit'], ['csrf']);

// SEO
$router->get('/sitemap.xml', [\App\Controllers\Public\SeoController::class, 'sitemap']);
$router->get('/robots.txt', [\App\Controllers\Public\SeoController::class, 'robots']);

// Dynamic CMS pages (catch-all — must come last among public routes)
$router->get('/page/{slug}', [\App\Controllers\Public\PageController::class, 'show']);

// ─── Admin Routes ───────────────────────────────────────────────────────────

$router->group(['prefix' => '/admin', 'middleware' => ['csrf']], function (Router $router) {

    // Auth (no auth middleware — guest only)
    $router->get('/login', [\App\Controllers\Admin\AuthController::class, 'loginForm'], ['guest']);
    $router->post('/login', [\App\Controllers\Admin\AuthController::class, 'login'], ['guest']);
    $router->get('/2fa', [\App\Controllers\Admin\AuthController::class, 'twoFactorForm']);
    $router->post('/2fa', [\App\Controllers\Admin\AuthController::class, 'twoFactor']);
    $router->get('/logout', [\App\Controllers\Admin\AuthController::class, 'logout'], ['auth']);
    $router->get('/forgot-password', [\App\Controllers\Admin\AuthController::class, 'forgotForm'], ['guest']);
    $router->post('/forgot-password', [\App\Controllers\Admin\AuthController::class, 'forgot'], ['guest']);
    $router->get('/reset-password/{token}', [\App\Controllers\Admin\AuthController::class, 'resetForm']);
    $router->post('/reset-password', [\App\Controllers\Admin\AuthController::class, 'reset']);

    // Protected admin area
    $router->group(['middleware' => ['auth']], function (Router $router) {

        // Dashboard
        $router->get('', [\App\Controllers\Admin\DashboardController::class, 'index']);
        $router->get('/', [\App\Controllers\Admin\DashboardController::class, 'index']);

        // Services
        $router->get('/services', [\App\Controllers\Admin\ServicesController::class, 'index']);
        $router->get('/services/create', [\App\Controllers\Admin\ServicesController::class, 'create']);
        $router->post('/services', [\App\Controllers\Admin\ServicesController::class, 'store']);
        $router->get('/services/{id}/edit', [\App\Controllers\Admin\ServicesController::class, 'edit']);
        $router->post('/services/{id}', [\App\Controllers\Admin\ServicesController::class, 'update']);
        $router->post('/services/reorder', [\App\Controllers\Admin\ServicesController::class, 'reorder']);

        // Projects
        $router->get('/projects', [\App\Controllers\Admin\ProjectsController::class, 'index']);
        $router->get('/projects/create', [\App\Controllers\Admin\ProjectsController::class, 'create']);
        $router->post('/projects', [\App\Controllers\Admin\ProjectsController::class, 'store']);
        $router->get('/projects/{id}/edit', [\App\Controllers\Admin\ProjectsController::class, 'edit']);
        $router->post('/projects/{id}', [\App\Controllers\Admin\ProjectsController::class, 'update']);

        // Case Studies
        $router->get('/case-studies', [\App\Controllers\Admin\CaseStudiesController::class, 'index']);
        $router->get('/case-studies/create', [\App\Controllers\Admin\CaseStudiesController::class, 'create']);
        $router->post('/case-studies', [\App\Controllers\Admin\CaseStudiesController::class, 'store']);
        $router->get('/case-studies/{id}/edit', [\App\Controllers\Admin\CaseStudiesController::class, 'edit']);
        $router->post('/case-studies/{id}', [\App\Controllers\Admin\CaseStudiesController::class, 'update']);

        // Articles
        $router->get('/articles', [\App\Controllers\Admin\ArticlesController::class, 'index']);
        $router->get('/articles/create', [\App\Controllers\Admin\ArticlesController::class, 'create']);
        $router->post('/articles', [\App\Controllers\Admin\ArticlesController::class, 'store']);
        $router->get('/articles/{id}/edit', [\App\Controllers\Admin\ArticlesController::class, 'edit']);
        $router->post('/articles/{id}', [\App\Controllers\Admin\ArticlesController::class, 'update']);

        // Leads
        $router->get('/leads', [\App\Controllers\Admin\LeadsController::class, 'index']);
        $router->get('/leads/export', [\App\Controllers\Admin\LeadsController::class, 'export']);
        $router->get('/leads/{id}', [\App\Controllers\Admin\LeadsController::class, 'show']);
        $router->post('/leads/{id}', [\App\Controllers\Admin\LeadsController::class, 'update']);
        $router->post('/leads/{id}/note', [\App\Controllers\Admin\LeadsController::class, 'addNote']);

        // Media
        $router->get('/media', [\App\Controllers\Admin\MediaController::class, 'index']);
        $router->post('/media/upload', [\App\Controllers\Admin\MediaController::class, 'upload']);
        $router->post('/media/{id}', [\App\Controllers\Admin\MediaController::class, 'update']);

        // Testimonials
        $router->get('/testimonials', [\App\Controllers\Admin\TestimonialsController::class, 'index']);
        $router->get('/testimonials/create', [\App\Controllers\Admin\TestimonialsController::class, 'create']);
        $router->post('/testimonials', [\App\Controllers\Admin\TestimonialsController::class, 'store']);
        $router->get('/testimonials/{id}/edit', [\App\Controllers\Admin\TestimonialsController::class, 'edit']);
        $router->post('/testimonials/{id}', [\App\Controllers\Admin\TestimonialsController::class, 'update']);

        // Industries
        $router->get('/industries', [\App\Controllers\Admin\IndustriesController::class, 'index']);
        $router->get('/industries/create', [\App\Controllers\Admin\IndustriesController::class, 'create']);
        $router->post('/industries', [\App\Controllers\Admin\IndustriesController::class, 'store']);
        $router->get('/industries/{id}/edit', [\App\Controllers\Admin\IndustriesController::class, 'edit']);
        $router->post('/industries/{id}', [\App\Controllers\Admin\IndustriesController::class, 'update']);

        // Locations
        $router->get('/locations', [\App\Controllers\Admin\LocationsController::class, 'index']);
        $router->get('/locations/create', [\App\Controllers\Admin\LocationsController::class, 'create']);
        $router->post('/locations', [\App\Controllers\Admin\LocationsController::class, 'store']);
        $router->get('/locations/{id}/edit', [\App\Controllers\Admin\LocationsController::class, 'edit']);
        $router->post('/locations/{id}', [\App\Controllers\Admin\LocationsController::class, 'update']);

        // Packages
        $router->get('/packages', [\App\Controllers\Admin\PackagesController::class, 'index']);
        $router->get('/packages/create', [\App\Controllers\Admin\PackagesController::class, 'create']);
        $router->post('/packages', [\App\Controllers\Admin\PackagesController::class, 'store']);
        $router->get('/packages/{id}/edit', [\App\Controllers\Admin\PackagesController::class, 'edit']);
        $router->post('/packages/{id}', [\App\Controllers\Admin\PackagesController::class, 'update']);

        // Audit requests
        $router->get('/audits', [\App\Controllers\Admin\AuditController::class, 'index']);
        $router->get('/audits/{id}', [\App\Controllers\Admin\AuditController::class, 'show']);
        $router->post('/audits/{id}/status', [\App\Controllers\Admin\AuditController::class, 'updateStatus']);

        // Users
        $router->get('/users', [\App\Controllers\Admin\UsersController::class, 'index']);
        $router->get('/users/create', [\App\Controllers\Admin\UsersController::class, 'create']);
        $router->post('/users', [\App\Controllers\Admin\UsersController::class, 'store']);
        $router->get('/users/{id}/edit', [\App\Controllers\Admin\UsersController::class, 'edit']);
        $router->post('/users/{id}', [\App\Controllers\Admin\UsersController::class, 'update']);
        $router->post('/users/{id}/delete', [\App\Controllers\Admin\UsersController::class, 'destroy']);
        $router->get('/profile', [\App\Controllers\Admin\UsersController::class, 'profile']);
        $router->post('/profile', [\App\Controllers\Admin\UsersController::class, 'updateProfile']);

        // Settings
        $router->get('/settings/general', [\App\Controllers\Admin\SettingsController::class, 'general']);
        $router->post('/settings/general', [\App\Controllers\Admin\SettingsController::class, 'saveGeneral']);
        $router->get('/settings/brand', [\App\Controllers\Admin\SettingsController::class, 'brand']);
        $router->post('/settings/brand', [\App\Controllers\Admin\SettingsController::class, 'saveBrand']);
        $router->get('/settings/seo', [\App\Controllers\Admin\SettingsController::class, 'seo']);
        $router->post('/settings/seo', [\App\Controllers\Admin\SettingsController::class, 'saveSeo']);
        $router->get('/settings/email', [\App\Controllers\Admin\SettingsController::class, 'email']);
        $router->post('/settings/email', [\App\Controllers\Admin\SettingsController::class, 'saveEmail']);
        $router->post('/settings/email/test', [\App\Controllers\Admin\SettingsController::class, 'testEmail']);
        $router->get('/settings/integrations', [\App\Controllers\Admin\SettingsController::class, 'integrations']);
        $router->post('/settings/integrations', [\App\Controllers\Admin\SettingsController::class, 'saveIntegrations']);
        $router->post('/settings/maintenance', [\App\Controllers\Admin\SettingsController::class, 'maintenanceMode']);

        // Menus
        $router->get('/menus', [\App\Controllers\Admin\MenusController::class, 'index']);
        $router->get('/menus/create', [\App\Controllers\Admin\MenusController::class, 'create']);
        $router->post('/menus', [\App\Controllers\Admin\MenusController::class, 'store']);
        $router->get('/menus/{id}/edit', [\App\Controllers\Admin\MenusController::class, 'edit']);
        $router->post('/menus/{id}', [\App\Controllers\Admin\MenusController::class, 'update']);
        $router->post('/menus/{id}/delete', [\App\Controllers\Admin\MenusController::class, 'destroy']);

        // Redirects
        $router->get('/redirects', [\App\Controllers\Admin\RedirectsController::class, 'index']);
        $router->get('/redirects/create', [\App\Controllers\Admin\RedirectsController::class, 'create']);
        $router->post('/redirects', [\App\Controllers\Admin\RedirectsController::class, 'store']);
        $router->get('/redirects/{id}/edit', [\App\Controllers\Admin\RedirectsController::class, 'edit']);
        $router->post('/redirects/{id}', [\App\Controllers\Admin\RedirectsController::class, 'update']);
        $router->post('/redirects/{id}/delete', [\App\Controllers\Admin\RedirectsController::class, 'destroy']);

        // Analytics
        $router->get('/analytics', [\App\Controllers\Admin\AnalyticsController::class, 'index']);
    });
});
