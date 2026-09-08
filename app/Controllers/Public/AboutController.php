<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;

class AboutController
{
    public function index(Request $request): void
    {
        echo View::render('about/index', [
            'title'       => 'About Us — The Paragon .Design',
            'metaDescription' => 'Learn about The Paragon .Design — a South African digital agency specialising in premium web design, branding, and digital marketing.',
            'team'        => [],
        ]);
    }
}
