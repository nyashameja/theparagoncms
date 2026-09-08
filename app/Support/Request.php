<?php

namespace App\Support;

class Request
{
    public readonly string $method;
    public readonly string $path;
    public readonly string $uri;
    public readonly array  $query;
    public readonly array  $body;
    public readonly array  $files;
    public readonly array  $headers;
    public readonly string $ip;

    public function __construct()
    {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri     = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path    = parse_url($this->uri, PHP_URL_PATH) ?? '/';
        $this->query   = $_GET;
        $this->body    = $_POST;
        $this->files   = $_FILES;
        $this->headers = getallheaders() ?: [];
        $this->ip      = $this->resolveIp();
    }

    public static function capture(): self
    {
        return new self();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }

    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    public function isPost(): bool { return $this->isMethod('POST'); }
    public function isGet(): bool  { return $this->isMethod('GET'); }

    public function isAjax(): bool
    {
        return ($this->headers['X-Requested-With'] ?? '') === 'XMLHttpRequest';
    }

    public function wantsJson(): bool
    {
        $accept = $this->headers['Accept'] ?? '';
        return str_contains($accept, 'application/json');
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $key, string $default = ''): string
    {
        return $this->headers[$key] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $auth = $this->header('Authorization');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }

    private function resolveIp(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = explode(',', $_SERVER[$key])[0];
                return trim($ip);
            }
        }
        return '0.0.0.0';
    }

    public function fullUrl(): string
    {
        $scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443) ? 'https' : 'http';
        return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $this->uri;
    }
}
