<?php

/**
 * Database seeder — seeds sample content for The Paragon .Design CMS.
 * Usage: php database/seeds/seed.php
 */

require_once dirname(__DIR__, 2) . '/bootstrap/app.php';

use App\Support\Database;
use App\Models\User;

echo "Seeding database...\n";

// ─── Roles ───────────────────────────────────────────────────────────────────
$roles = [
    ['name' => 'Administrator', 'slug' => 'admin',        'description' => 'Full access'],
    ['name' => 'Editor',        'slug' => 'editor',       'description' => 'Content editing'],
    ['name' => 'Lead Manager',  'slug' => 'lead_manager', 'description' => 'Lead management only'],
];
foreach ($roles as $role) {
    Database::selectOne('SELECT id FROM roles WHERE slug = ?', [$role['slug']]) ||
    Database::insert('INSERT INTO roles (name, slug, description, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())', array_values($role));
}
echo "  ✓ Roles\n";

// ─── Default admin user ───────────────────────────────────────────────────────
$admin = User::findByEmail('admin@theparagondesign.com');
if (!$admin) {
    $userId = User::create([
        'name'          => 'Paragon Admin',
        'email'         => 'admin@theparagondesign.com',
        'password_hash' => User::hashPassword('ChangeMe!2024'),
        'role'          => 'admin',
        'is_active'     => 1,
    ]);
    echo "  ✓ Admin user created (email: admin@theparagondesign.com, password: ChangeMe!2024)\n";
    echo "  ! IMPORTANT: Change the admin password immediately after first login.\n";
}

// ─── Settings ────────────────────────────────────────────────────────────────
$settings = [
    'business_name'          => 'The Paragon .Design',
    'legal_name'             => 'The Paragon Design (Pty) Ltd',
    'tagline'                => 'Premium Digital Design Agency',
    'contact_email'          => 'hello@theparagondesign.com',
    'contact_phone'          => '+27 11 000 0000',
    'whatsapp_number'        => '27110000000',
    'whatsapp_default_message' => 'Hi, I\'d like to discuss a project with The Paragon .Design.',
    'address_line1'          => '1 Design Street',
    'address_city'           => 'Randburg',
    'address_province'       => 'Gauteng',
    'address_postal'         => '2194',
    'address_country'        => 'South Africa',
    'site_title'             => 'The Paragon .Design — Premium Digital Agency in Johannesburg',
    'site_description'       => 'The Paragon .Design builds high-performance websites, e-commerce solutions, and digital strategies that grow South African businesses.',
    'color_primary_bg'       => '#FFFFFF',
    'color_alt_bg'           => '#F5F5F3',
    'color_primary_text'     => '#111111',
    'color_secondary_text'   => '#5E5E5E',
    'color_strong_black'     => '#050505',
    'color_accent'           => '#D71920',
    'color_accent_dark'      => '#A80F17',
    'color_border'           => '#E6E6E3',
    'color_success'          => '#16845B',
    'color_warning'          => '#C47A12',
    'font_primary'           => 'Inter',
    'font_secondary'         => 'Inter',
    'social_instagram'       => 'https://instagram.com/theparagondesign',
    'social_linkedin'        => 'https://linkedin.com/company/theparagondesign',
    'maintenance_mode'       => '0',
    'privacy_policy_content' => '<p>Last updated: ' . date('d F Y') . '</p><p>This Privacy Policy explains how The Paragon .Design ("we", "us", "our") collects, uses and protects personal information in compliance with the Protection of Personal Information Act (POPIA), Act 4 of 2013.</p><h2>Information We Collect</h2><p>We collect information you provide directly, such as your name, email address, phone number and project details when you complete a form on our website.</p><h2>How We Use Your Information</h2><p>We use your information to respond to enquiries, provide services, send relevant communications and improve our website.</p><h2>Data Retention</h2><p>We retain your data for as long as necessary to fulfil the purposes outlined in this policy, subject to your right to request deletion.</p><h2>Your Rights</h2><p>You have the right to access, correct or delete your personal information. Contact us at hello@theparagondesign.com to exercise these rights.</p>',
    'terms_content'          => '<p>Last updated: ' . date('d F Y') . '</p><p>By using the The Paragon .Design website, you agree to these Terms of Service.</p><h2>Services</h2><p>We provide digital design, development, and marketing services as described on our website.</p><h2>Intellectual Property</h2><p>All content on this website is owned by The Paragon .Design unless otherwise stated.</p>',
    'cookie_policy_content'  => '<p>We use cookies to improve your experience on our website. Essential cookies are required for the site to function. Analytics cookies help us understand how visitors use our site.</p>',
];

foreach ($settings as $key => $value) {
    $existing = Database::selectOne('SELECT id FROM settings WHERE `key` = ?', [$key]);
    if (!$existing) {
        Database::insert('INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())', [$key, $value]);
    }
}
echo "  ✓ Settings\n";

// ─── Menus ────────────────────────────────────────────────────────────────────
$menuLocations = [
    ['name' => 'Primary Navigation', 'location' => 'primary'],
    ['name' => 'Footer Navigation',  'location' => 'footer'],
];

foreach ($menuLocations as $menu) {
    $existing = Database::selectOne('SELECT id FROM menus WHERE location = ?', [$menu['location']]);
    if (!$existing) {
        $menuId = Database::insert('INSERT INTO menus (name, location, is_active, created_at, updated_at) VALUES (?, ?, 1, NOW(), NOW())', [$menu['name'], $menu['location']]);

        if ($menu['location'] === 'primary') {
            $primaryItems = [
                ['label' => 'Services', 'url' => '/services',     'sort_order' => 1],
                ['label' => 'Work',     'url' => '/work',         'sort_order' => 2],
                ['label' => 'About',    'url' => '/about',        'sort_order' => 3],
                ['label' => 'Insights', 'url' => '/insights',     'sort_order' => 4],
                ['label' => 'Contact',  'url' => '/contact',      'sort_order' => 5],
            ];
            foreach ($primaryItems as $item) {
                Database::insert('INSERT INTO menu_items (menu_id, parent_id, label, url, sort_order, is_active, created_at, updated_at) VALUES (?, 0, ?, ?, ?, 1, NOW(), NOW())',
                    [$menuId, $item['label'], $item['url'], $item['sort_order']]);
            }
        }
    }
}
echo "  ✓ Menus\n";

// ─── Services ─────────────────────────────────────────────────────────────────
$services = [
    ['name' => 'Web Design & Development',   'slug' => 'web-design-development',   'sort_order' => 1, 'icon' => 'globe',      'is_featured' => 1],
    ['name' => 'WordPress Development',      'slug' => 'wordpress-development',     'sort_order' => 2, 'icon' => 'code',       'is_featured' => 1],
    ['name' => 'E-Commerce Development',     'slug' => 'ecommerce-development',     'sort_order' => 3, 'icon' => 'shopping-bag','is_featured' => 1],
    ['name' => 'Graphic Design',             'slug' => 'graphic-design',            'sort_order' => 4, 'icon' => 'pen-tool',   'is_featured' => 0],
    ['name' => 'Branding & Corporate Identity','slug' => 'branding-corporate-identity','sort_order' => 5,'icon' => 'award',    'is_featured' => 1],
    ['name' => 'Search Engine Optimisation', 'slug' => 'seo',                       'sort_order' => 6, 'icon' => 'search',     'is_featured' => 1],
    ['name' => 'Website Hosting',            'slug' => 'website-hosting',           'sort_order' => 7, 'icon' => 'server',     'is_featured' => 0],
    ['name' => 'Website Care & Maintenance', 'slug' => 'website-care-maintenance',  'sort_order' => 8, 'icon' => 'shield',     'is_featured' => 0],
    ['name' => 'Digital Marketing',          'slug' => 'digital-marketing',         'sort_order' => 9, 'icon' => 'trending-up','is_featured' => 1],
    ['name' => 'Social Media Marketing',     'slug' => 'social-media-marketing',    'sort_order' => 10,'icon' => 'share-2',    'is_featured' => 0],
    ['name' => 'Email Marketing',            'slug' => 'email-marketing',           'sort_order' => 11,'icon' => 'mail',       'is_featured' => 0],
    ['name' => 'Workflow Automation',        'slug' => 'workflow-automation',       'sort_order' => 12,'icon' => 'zap',        'is_featured' => 0],
];

$serviceDescriptions = [
    'web-design-development'   => ['short' => 'Custom websites that perform as well as they look. We build fast, accessible, mobile-first websites engineered for search and conversion.', 'hero' => 'Websites That Win Clients'],
    'wordpress-development'    => ['short' => 'Powerful WordPress websites with clean code, secure architecture, and content management that your team can use confidently.', 'hero' => 'WordPress Done Properly'],
    'ecommerce-development'    => ['short' => 'E-commerce solutions built to sell. From product catalogues to checkout flows, we build stores that turn browsers into buyers.', 'hero' => 'Online Stores Built to Sell'],
    'branding-corporate-identity' => ['short' => 'Strategic brand identities that position you correctly and look brilliant across every application.', 'hero' => 'Brands That Mean Something'],
    'seo'                      => ['short' => 'Search engine optimisation that drives qualified traffic and measurable growth — not vanity rankings.', 'hero' => 'Be Found. Be Chosen.'],
    'digital-marketing'        => ['short' => 'Integrated digital marketing strategies that reach the right audience at the right time with the right message.', 'hero' => 'Marketing That Moves the Needle'],
];

foreach ($services as $service) {
    $existing = Database::selectOne('SELECT id FROM services WHERE slug = ?', [$service['slug']]);
    if (!$existing) {
        $desc = $serviceDescriptions[$service['slug']] ?? ['short' => '', 'hero' => $service['name']];
        Database::insert(
            'INSERT INTO services (name, slug, short_description, hero_headline, status, is_featured, sort_order, icon, primary_cta_label, primary_cta_url, secondary_cta_label, secondary_cta_url, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [$service['name'], $service['slug'], $desc['short'], $desc['hero'], 'published', $service['is_featured'], $service['sort_order'], $service['icon'], 'Get a Quote', '/quote', 'Learn More', '/services/' . $service['slug']]
        );
    }
}
echo "  ✓ Services\n";

// ─── Industries ───────────────────────────────────────────────────────────────
$industries = [
    ['name' => 'Retail & E-Commerce',   'slug' => 'retail-ecommerce',   'sort_order' => 1, 'is_featured' => 1],
    ['name' => 'Professional Services', 'slug' => 'professional-services','sort_order' => 2,'is_featured' => 1],
    ['name' => 'Construction & Property','slug' => 'construction-property','sort_order' => 3,'is_featured' => 1],
    ['name' => 'Healthcare & Medical',  'slug' => 'healthcare-medical',  'sort_order' => 4, 'is_featured' => 1],
    ['name' => 'Hospitality & Tourism', 'slug' => 'hospitality-tourism', 'sort_order' => 5, 'is_featured' => 1],
    ['name' => 'Education & Training',  'slug' => 'education-training',  'sort_order' => 6, 'is_featured' => 1],
    ['name' => 'Finance & Insurance',   'slug' => 'finance-insurance',   'sort_order' => 7, 'is_featured' => 1],
    ['name' => 'Manufacturing & Industrial','slug' => 'manufacturing-industrial','sort_order' => 8,'is_featured' => 1],
];

foreach ($industries as $ind) {
    $existing = Database::selectOne('SELECT id FROM industries WHERE slug = ?', [$ind['slug']]);
    if (!$existing) {
        Database::insert(
            'INSERT INTO industries (name, slug, status, is_featured, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            [$ind['name'], $ind['slug'], 'published', $ind['is_featured'], $ind['sort_order']]
        );
    }
}
echo "  ✓ Industries\n";

// ─── Locations ────────────────────────────────────────────────────────────────
$locations = [
    ['name' => 'Johannesburg', 'slug' => 'johannesburg', 'type' => 'primary',      'sort_order' => 1],
    ['name' => 'Randburg',     'slug' => 'randburg',     'type' => 'primary',      'sort_order' => 2],
    ['name' => 'Pretoria',     'slug' => 'pretoria',     'type' => 'service_area', 'sort_order' => 3],
    ['name' => 'Sandton',      'slug' => 'sandton',      'type' => 'service_area', 'sort_order' => 4],
    ['name' => 'Midrand',      'slug' => 'midrand',      'type' => 'service_area', 'sort_order' => 5],
];

foreach ($locations as $loc) {
    $existing = Database::selectOne('SELECT id FROM locations WHERE slug = ?', [$loc['slug']]);
    if (!$existing) {
        $heading = 'Web Design & Digital Marketing in ' . $loc['name'];
        $description = 'The Paragon .Design serves businesses in ' . $loc['name'] . ' and surrounding areas with premium website design, SEO, and digital marketing services.';
        Database::insert(
            'INSERT INTO locations (name, slug, type, heading, description, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [$loc['name'], $loc['slug'], $loc['type'], $heading, $description, 'published', $loc['sort_order']]
        );
    }
}
echo "  ✓ Locations\n";

// ─── Trust Statistics (placeholder — unpublished) ─────────────────────────────
$stats = [
    ['label' => 'Projects Delivered', 'value' => '50+',  'icon' => 'check-circle'],
    ['label' => 'Happy Clients',       'value' => '40+',  'icon' => 'users'],
    ['label' => 'Years of Experience', 'value' => '5+',   'icon' => 'calendar'],
    ['label' => 'Industries Served',   'value' => '12+',  'icon' => 'briefcase'],
];
foreach ($stats as $stat) {
    $existing = Database::selectOne('SELECT id FROM trust_statistics WHERE label = ?', [$stat['label']]);
    if (!$existing) {
        Database::insert(
            'INSERT INTO trust_statistics (label, value, icon, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, 1, 0, NOW(), NOW())',
            [$stat['label'], $stat['value'], $stat['icon']]
        );
    }
}
echo "  ✓ Statistics\n";

// ─── Sample Article Categories ────────────────────────────────────────────────
$categories = [
    ['name' => 'Web Design',        'slug' => 'web-design'],
    ['name' => 'SEO & Marketing',   'slug' => 'seo-marketing'],
    ['name' => 'Branding',          'slug' => 'branding'],
    ['name' => 'Business Tips',     'slug' => 'business-tips'],
    ['name' => 'Case Studies',      'slug' => 'case-studies'],
];
foreach ($categories as $cat) {
    $existing = Database::selectOne('SELECT id FROM article_categories WHERE slug = ?', [$cat['slug']]);
    if (!$existing) {
        Database::insert(
            'INSERT INTO article_categories (name, slug, sort_order, created_at, updated_at) VALUES (?, ?, 0, NOW(), NOW())',
            [$cat['name'], $cat['slug']]
        );
    }
}
echo "  ✓ Article categories\n";

// ─── Pages ────────────────────────────────────────────────────────────────────
$existingHome = Database::selectOne('SELECT id FROM pages WHERE slug = \'home\' LIMIT 1');
if (!$existingHome) {
    Database::insert(
        'INSERT INTO pages (title, slug, template, status, meta_title, meta_description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())',
        ['Home', 'home', 'home', 'published', 'The Paragon .Design — Premium Digital Agency in Johannesburg', 'Premium web design, branding, SEO and digital marketing from The Paragon .Design, Johannesburg.']
    );
}
echo "  ✓ Home page\n";

echo "\nSeeding complete.\n";
echo "Admin login: admin@theparagondesign.com / ChangeMe!2024\n";
echo "IMPORTANT: Change the admin password immediately.\n";
