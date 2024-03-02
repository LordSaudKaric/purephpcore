<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Server;

/**
 *
 */
class Server
{
    private function __construct()
    {
    }

    public static function all(): array
    {
        return $_SERVER;
    }

    public static function has(string $key): bool
    {
        return isset($_SERVER[$key]);
    }

    public static function get(string $key): string
    {
        return self::has($key) ? $_SERVER[$key] : '';
    }

    public static function path_info(string $path): array
    {
        return pathinfo($path);
    }
}