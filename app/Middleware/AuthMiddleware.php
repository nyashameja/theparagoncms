<?php

namespace App\Middleware;

use App\Support\Request;
use App\Support\Session;

class AuthMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please log in to continue.');
            Session::flash('intended', $request->uri);
            redirect('/admin/login');
        }

        // Inactivity timeout (120 minutes)
        $lastActivity = Session::get('_last_activity', time());
        if (time() - $lastActivity > 7200) {
            Session::destroy();
            Session::start();
            Session::flash('error', 'Your session has expired. Please log in again.');
            redirect('/admin/login');
        }
        Session::set('_last_activity', time());

        $next();
    }
}
