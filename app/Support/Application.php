<?php

namespace App\Support;

class Application
{
    private static array $bindings = [];
    private static array $instances = [];

    public function bind(string $abstract, callable $factory): void
    {
        self::$bindings[$abstract] = $factory;
    }

    public function singleton(string $abstract, callable $factory): void
    {
        self::$bindings[$abstract] = function () use ($abstract, $factory) {
            if (!isset(self::$instances[$abstract])) {
                self::$instances[$abstract] = $factory($this);
            }
            return self::$instances[$abstract];
        };
    }

    public function make(string $abstract): mixed
    {
        if (isset(self::$bindings[$abstract])) {
            return (self::$bindings[$abstract])($this);
        }
        if (class_exists($abstract)) {
            return new $abstract();
        }
        throw new \RuntimeException("Cannot resolve: $abstract");
    }

    public function run(): void
    {
        $request = Request::capture();
        $router  = new Router();

        require_once base_path('routes/web.php');

        $router->dispatch($request);
    }
}
