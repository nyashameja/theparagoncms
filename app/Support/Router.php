<?php

namespace App\Support;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private string $prefix = '';
    private array $groupMiddleware = [];

    public function get(string $path, array|callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array|callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, array|callable|string $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function group(array $attributes, callable $callback): void
    {
        $prevPrefix     = $this->prefix;
        $prevMiddleware = $this->groupMiddleware;

        $this->prefix          = $prevPrefix . ($attributes['prefix'] ?? '');
        $this->groupMiddleware = array_merge($prevMiddleware, $attributes['middleware'] ?? []);

        $callback($this);

        $this->prefix          = $prevPrefix;
        $this->groupMiddleware = $prevMiddleware;
    }

    private function addRoute(string $method, string $path, mixed $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $this->prefix . $path,
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method;
        $path   = rtrim($request->path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = $this->compilePattern($route['path']);
            if (!preg_match($pattern, $path, $matches)) continue;

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            $this->runMiddleware($route['middleware'], $request, function () use ($route, $request, $params) {
                $this->callHandler($route['handler'], $request, $params);
            });
            return;
        }

        // No route matched — check for redirects
        $redirect = \App\Models\Redirect::findBySource($path);
        if ($redirect) {
            header('Location: ' . $redirect['target'], true, (int) $redirect['code']);
            exit;
        }

        // Log 404
        \App\Models\NotFoundLog::record($path, $request->fullUrl(), $request->header('Referer'), $request->ip);

        abort(404, 'Page not found');
    }

    private function compilePattern(string $path): string
    {
        $path    = rtrim($path, '/') ?: '/';
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#u';
    }

    private function runMiddleware(array $middleware, Request $request, callable $next): void
    {
        if (empty($middleware)) {
            $next();
            return;
        }

        $mw = array_shift($middleware);
        $class = $this->resolveMiddleware($mw);
        (new $class())->handle($request, function () use ($middleware, $request, $next) {
            $this->runMiddleware($middleware, $request, $next);
        });
    }

    private function resolveMiddleware(string $alias): string
    {
        $map = [
            'auth'       => \App\Middleware\AuthMiddleware::class,
            'guest'      => \App\Middleware\GuestMiddleware::class,
            'csrf'       => \App\Middleware\CsrfMiddleware::class,
            'role'       => \App\Middleware\RoleMiddleware::class,
            'throttle'   => \App\Middleware\ThrottleMiddleware::class,
        ];
        return $map[$alias] ?? $alias;
    }

    private function callHandler(mixed $handler, Request $request, array $params): void
    {
        if (is_callable($handler)) {
            echo $handler($request, $params);
            return;
        }

        if (is_array($handler)) {
            [$class, $method] = $handler;
        } elseif (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
        } else {
            throw new \RuntimeException("Invalid handler: " . print_r($handler, true));
        }

        $controller = new $class();
        $result = $controller->$method($request, ...$params);
        if (is_string($result)) echo $result;
    }
}
