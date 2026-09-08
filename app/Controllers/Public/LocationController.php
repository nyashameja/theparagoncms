<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class LocationController
{
    public function show(Request $request, string $slug): void
    {
        $location = Database::selectOne(
            "SELECT * FROM locations WHERE slug=? AND is_active=1 AND deleted_at IS NULL",
            [$slug]
        );
        if (!$location) abort(404);

        $services = Database::select(
            "SELECT * FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 6"
        );

        echo View::render('locations/show', [
            'title'           => 'Web Design ' . ($location['type'] === 'primary' ? 'in ' : 'for ') . $location['name'] . ' — The Paragon .Design',
            'metaDescription' => $location['meta_description'] ?? ('Premium web design and digital marketing ' . ($location['type'] === 'primary' ? 'in ' : 'for ') . $location['name'] . '.'),
            'location'        => $location,
            'services'        => $services,
        ]);
    }
}
