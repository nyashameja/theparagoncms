<?php

namespace App\Support;

class Csrf
{
    public static function token(): string
    {
        if (!Session::has('_csrf_token')) {
            Session::set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('_csrf_token');
    }

    public static function verify(string $token): bool
    {
        $stored = Session::get('_csrf_token');
        if (!$stored) return false;
        return hash_equals($stored, $token);
    }

    public static function regenerate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('_csrf_token', $token);
        return $token;
    }
}
