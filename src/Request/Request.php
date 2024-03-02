<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Request;

use Lordsaudkaric\Purephp\Server\Server;

class Request
{
    private static string $base_url;
    private static string $url;
    private static string $full_url;
    private static string $query_string;
    private static string $script_name;

    private function __construct()
    {
    }

    /**
     *
     */
    public static function handle(): void
    {
        self::setBaseUrl();
        self::setUrl();
    }

    /**
     *
     */
    private static function setBaseUrl(): void
    {
        $protocol = Server::get('REQUEST_SCHEME') . '://';
        $host_name = Server::get('SERVER_NAME');
        $script_name = self::$script_name = str_replace('\\', '', dirname(Server::get('SCRIPT_NAME')));

        self::$base_url = $protocol . $host_name . $script_name;
    }

    /**
     *
     */
    private static function setUrl(): void
    {
        $request_uri = urldecode(Server::get('REQUEST_URI'));
        $request_uri = rtrim(preg_replace('#^' . self::$script_name . '#', '', $request_uri), '/');

        self::$full_url = $request_uri;
        self::$url = parse_url($request_uri)['path'] ?: '/';
        self::$query_string = parse_url($request_uri)['query'] ?? '';
    }

    /**
     * @return string
     */
    public static function baseUrl(): string
    {
        return self::$base_url;
    }

    /**
     * @return string
     */
    public static function url(): string
    {
        return self::$url;
    }

    /**
     * @return string
     */
    public static function query_string(): string
    {
        return self::$query_string;
    }

    /**
     * @return string
     */
    public static function full_url(): string
    {
        return self::$full_url;
    }

    public static function method(): string
    {
        return strtolower(Server::get('REQUEST_METHOD'));
    }

    public static function has(array $type, string $key): bool
    {
        return array_key_exists($key, $type);
    }

    public static function value(string $key, array $type = null)
    {
        $type = $type ?? $_REQUEST;

        return self::has($type, $key) ? $type[$key] : null;
    }

    public static function get(string $key): ?string
    {
        return self::value($key, $_GET);
    }

    public static function post(string $key): ?string
    {
        return self::value($key, $_POST);
    }

    public static function set(string $key, string $value): string
    {
        $_REQUEST[$key] = $value;
        $_POST[$key] = $value;
        $_GET[$key] = $value;

        return $value;
    }

    public static function previous(): string
    {
        return Server::get('HTTP_REFERER');
    }

    public static function all(): array
    {
        return $_REQUEST;
    }
}