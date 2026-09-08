<?php

namespace App\Middleware;

use App\Support\Request;
use App\Support\Session;

class RoleMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        $userRole = Session::get('user_role', '');

        // Roles in descending privilege: superadmin > admin > editor > viewer
        $allowed = ['superadmin', 'admin'];

        if (!in_array($userRole, $allowed, true)) {
            Session::flash('error', 'You do not have permission to access that area.');
            redirect('/admin');
        }

        $next();
    }
}
