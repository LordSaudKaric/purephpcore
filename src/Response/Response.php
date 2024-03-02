<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Response;

class Response
{
    private function __construct()
    {
    }

    public static function output(mixed $data)
    {
        if (!$data) return;

        if (!is_string($data)) {
            $data = self::json($data);
        }
        echo $data;
    }

    public static function json(mixed $data)
    {
        return json_encode($data);
    }
}