<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Exceptions;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Whoops
{
    private function __construct()
    {
    }

    public static function handle()
    {
        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();
    }
}