<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class PackagesController
{
    public function index(Request $request): void
    {
        $packages = Database::select(
            "SELECT * FROM packages WHERE is_active=1 AND deleted_at IS NULL ORDER BY sort_order ASC"
        );

        /* Decode included_items JSON for each package */
        foreach ($packages as &$pkg) {
            if (!empty($pkg['included_items'])) {
                $decoded = json_decode($pkg['included_items'], true);
                $pkg['included_items'] = is_array($decoded) ? $decoded : [$pkg['included_items']];
            }
        }
        unset($pkg);

        View::render('packages/index', [
            'title'           => 'Packages & Pricing — The Paragon .Design',
            'metaDescription' => 'Transparent pricing for premium web design, branding, and digital marketing services. All packages are customisable.',
            'packages'        => $packages,
        ]);
    }
}
