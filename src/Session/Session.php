<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Session;

class Session
{
    private function __construct()
    {
    }

    /**
     * @return void
     */
    public static function start(): void
    {
        if (!session_id()) {
            ini_set('session.use_only_cookies', '1');
            session_start();
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function set(string $key, mixed $value): mixed
    {
        return $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return self::has($key) ? $_SESSION[$key] : null;
    }

    /**
     * @param string $key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        return $_SESSION;
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

    /**
     * @param string $key
     * @return string
     */
    public static function flash(string $key): mixed
    {
        $value = null;
        if (self::has($key)) {
            $value = self::get($key);
            self::remove($key);
        }
        return $value;
    }
}