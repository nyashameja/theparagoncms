<?php

namespace App\Support;

class View
{
    private static array $shared = [];
    private static array $sections = [];
    private static array $sectionStack = [];
    private static string $layout = '';

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = []): string
    {
        self::$layout = '';
        self::$sections = [];
        self::$sectionStack = [];

        $file = resource_path('views/' . str_replace('.', '/', $template) . '.php');
        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: $template ($file)");
        }

        $data = array_merge(self::$shared, $data);
        $content = self::renderFile($file, $data);

        if (self::$layout) {
            $layoutFile = resource_path('views/' . str_replace('.', '/', self::$layout) . '.php');
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: " . self::$layout);
            }
            $data['_view_content'] = $content;
            $content = self::renderFile($layoutFile, $data);
        }

        return $content;
    }

    public static function renderFile(string $file, array $data = []): string
    {
        $data = array_merge(self::$shared, $data);
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }

    public static function extend(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function section(string $name): void
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    public static function endSection(): void
    {
        $name = array_pop(self::$sectionStack);
        self::$sections[$name] = ob_get_clean();
    }

    public static function yield(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }

    public static function include(string $template, array $data = []): string
    {
        $file = resource_path('views/' . str_replace('.', '/', $template) . '.php');
        if (!file_exists($file)) {
            throw new \RuntimeException("Partial not found: $template");
        }
        return self::renderFile($file, $data);
    }

    public static function component(string $name, array $data = []): string
    {
        return self::include('partials.' . $name, $data);
    }
}
