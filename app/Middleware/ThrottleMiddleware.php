<?php

namespace App\Middleware;

use App\Support\Request;
use App\Support\RateLimit;

class ThrottleMiddleware
{
    private int $maxAttempts = 60;
    private int $decaySeconds = 60;

    public function handle(Request $request, callable $next): void
    {
        $key = 'throttle:' . $request->ip . ':' . $request->path;

        if (!RateLimit::check($key, $this->maxAttempts, $this->decaySeconds)) {
            http_response_code(429);
            if ($request->wantsJson()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Too many requests. Please try again later.']);
            } else {
                echo view('errors/429', ['message' => 'Too many requests. Please slow down.']);
            }
            exit;
        }

        $next();
    }
}
