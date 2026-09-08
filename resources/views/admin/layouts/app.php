<!DOCTYPE html>
<html lang="en-ZA" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin') ?> — The Paragon .Design</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="shortcut icon" href="<?= e($brand['favicon'] ?? asset('images/favicon.ico')) ?>">
</head>
<body class="admin-body">

<?php if (\App\Support\Session::has('user_id')): ?>
<!-- Sidebar -->
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/admin" class="sidebar-logo">
            <?php if (!empty($brand['logo_dark'])): ?>
                <img src="<?= e($brand['logo_dark']) ?>" alt="<?= e($brand['name'] ?? 'Paragon') ?>" height="32">
            <?php else: ?>
                <span class="sidebar-logo-text">Paragon</span>
            <?php endif; ?>
        </a>
        <button class="sidebar-toggle" id="sidebarClose" aria-label="Close menu">✕</button>
    </div>

    <nav class="sidebar-nav" aria-label="Admin navigation">
        <a href="/admin" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin') && rtrim($_SERVER['REQUEST_URI'], '/') === '/admin' ? 'active' : '' ?>">
            <span class="nav-icon">⊞</span> Dashboard
        </a>

        <div class="nav-group-label">Content</div>
        <a href="/admin/services" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/services') ? 'active' : '' ?>">
            <span class="nav-icon">◈</span> Services
        </a>
        <a href="/admin/projects" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/projects') ? 'active' : '' ?>">
            <span class="nav-icon">◉</span> Projects
        </a>
        <a href="/admin/case-studies" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/case-studies') ? 'active' : '' ?>">
            <span class="nav-icon">◎</span> Case Studies
        </a>
        <a href="/admin/articles" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/articles') ? 'active' : '' ?>">
            <span class="nav-icon">▤</span> Articles
        </a>
        <a href="/admin/testimonials" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/testimonials') ? 'active' : '' ?>">
            <span class="nav-icon">★</span> Testimonials
        </a>
        <a href="/admin/packages" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/packages') ? 'active' : '' ?>">
            <span class="nav-icon">⊟</span> Packages
        </a>

        <div class="nav-group-label">Leads & Enquiries</div>
        <a href="/admin/leads" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/leads') ? 'active' : '' ?>">
            <span class="nav-icon">◐</span> Lead Inbox
        </a>
        <a href="/admin/audits" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/audits') ? 'active' : '' ?>">
            <span class="nav-icon">◑</span> Audit Requests
        </a>

        <div class="nav-group-label">Media & Assets</div>
        <a href="/admin/media" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/media') ? 'active' : '' ?>">
            <span class="nav-icon">▣</span> Media Library
        </a>

        <div class="nav-group-label">Structure</div>
        <a href="/admin/menus" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/menus') ? 'active' : '' ?>">
            <span class="nav-icon">☰</span> Menus
        </a>
        <a href="/admin/industries" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/industries') ? 'active' : '' ?>">
            <span class="nav-icon">◻</span> Industries
        </a>
        <a href="/admin/locations" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/locations') ? 'active' : '' ?>">
            <span class="nav-icon">◌</span> Locations
        </a>
        <a href="/admin/redirects" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/redirects') ? 'active' : '' ?>">
            <span class="nav-icon">↗</span> Redirects
        </a>

        <div class="nav-group-label">Analytics</div>
        <a href="/admin/analytics" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/analytics') ? 'active' : '' ?>">
            <span class="nav-icon">↗</span> Analytics
        </a>

        <div class="nav-group-label">System</div>
        <a href="/admin/users" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/users') ? 'active' : '' ?>">
            <span class="nav-icon">◯</span> Users
        </a>
        <a href="/admin/settings/general" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
            <span class="nav-icon">⊕</span> Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/admin/profile" class="sidebar-user">
            <span><?= e(\App\Support\Session::get('user_name', 'Admin')) ?></span>
        </a>
        <a href="/admin/logout" class="sidebar-logout">Sign Out</a>
    </div>
</aside>
<?php endif; ?>

<!-- Main content -->
<div class="admin-main" id="adminMain">
    <!-- Top bar -->
    <header class="admin-topbar">
        <button class="topbar-menu-btn" id="sidebarOpen" aria-label="Open menu">☰</button>
        <div class="topbar-title"><?= e($title ?? 'Dashboard') ?></div>
        <div class="topbar-actions">
            <a href="/" target="_blank" class="topbar-preview-link">View Site →</a>
        </div>
    </header>

    <!-- Flash messages -->
    <div class="flash-messages">
        <?php if (\App\Support\Session::hasFlash('success')): ?>
            <div class="flash flash-success" role="alert"><?= e(\App\Support\Session::getFlash('success')) ?></div>
        <?php endif; ?>
        <?php if (\App\Support\Session::hasFlash('error')): ?>
            <div class="flash flash-error" role="alert"><?= e(\App\Support\Session::getFlash('error')) ?></div>
        <?php endif; ?>
    </div>

    <!-- Page content -->
    <div class="admin-content">
        <?= \App\Support\View::yield('content') ?>
    </div>
</div>

<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
