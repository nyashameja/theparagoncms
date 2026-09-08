<?php

namespace App\Middleware;

use App\Support\Request;
use App\Support\Session;

class GuestMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        if (Session::has('user_id')) {
            redirect('/admin');
        }
        $next();
    }
}
