<?php

namespace App\Middleware;

use App\Support\Request;
use App\Support\Csrf;

class CsrfMiddleware
{
    private array $except = [
        '/api/webhook',
        '/api/leadforge',
    ];

    public function handle(Request $request, callable $next): void
    {
        if (in_array($request->method, ['GET', 'HEAD', 'OPTIONS'])) {
            $next();
            return;
        }

        foreach ($this->except as $path) {
            if (str_starts_with($request->path, $path)) {
                $next();
                return;
            }
        }

        $token = $request->get('_token') ?? $request->header('X-CSRF-Token');

        if (!$token || !Csrf::verify($token)) {
            http_response_code(419);
            if ($request->wantsJson() || $request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'CSRF token mismatch.']);
            } else {
                echo view('errors/419', ['message' => 'Your session has expired. Please go back and try again.']);
            }
            exit;
        }

        $next();
    }
}
