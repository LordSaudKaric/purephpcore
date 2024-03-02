<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Url;

use Lordsaudkaric\Purephp\Request\Request;

class Url
{
    public function __construct()
    {
    }

    public static function path(string $path)
    {
        return Request::baseUrl() . '/' . trim($path, '/');
    }

    public static function previous(): string
    {
        return Request::previous();
    }

    public static function redirect(string $path): void
    {
        header('Location:' . $path);
        exit();
    }
}