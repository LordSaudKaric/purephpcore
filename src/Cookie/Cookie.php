<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Cookie;

class Cookie
{
    private function __construct()
    {
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function set(string $key, $value)
    {
        setcookie($key, $value, strtotime('+30 days'), '/', '', false, true);
        return $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return self::has($key) ? $_COOKIE[$key] : null;
    }

    /**
     * @param string $key
     */
    public static function remove(string $key): void
    {
        setcookie($key, '', -1, '/', '', false, true);
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        return $_COOKIE;
    }

    /**
     *
     */
    public static function destry(): void
    {
        foreach (self::all() as $key => $value) {
            self::remove($key);
        }
    }
}