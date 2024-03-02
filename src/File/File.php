<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\File;

class File
{
    private function __construct()
    {
    }

    /**
     * @param string $path
     * @return string
     */
    public static function path(string $path): string
    {
        $path = ROOT . DS . trim($path, '/');
        $path = str_replace(['/', '\\'], DS, $path);

        return $path;
    }

    public static function exists(string $path): bool
    {
        return file_exists(self::path($path));
    }

    public static function require_file(string $path): mixed
    {
        if (self::exists($path)) {
            return require_once self::path($path);
        }
    }

    public static function include_file(string $path): mixed
    {
        if (self::exists($path)) {
            return include_once self::path($path);
        }
    }

    public static function require_directory(string $path): void
    {
        $files = array_diff(scandir(self::path($path)), ['.', '..']);

        foreach ($files as $file) {
            $file_path = $path . DS . $file;

            if (self::exists($file_path)) {
                self::require_file($file_path);
            }
        }
    }
}