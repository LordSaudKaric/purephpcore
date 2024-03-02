<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Bootstrap;

use Lordsaudkaric\Purephp\Exceptions\Whoops;
use Lordsaudkaric\Purephp\File\File;
use Lordsaudkaric\Purephp\Request\Request;
use Lordsaudkaric\Purephp\Response\Response;
use Lordsaudkaric\Purephp\Router\Route;
use Lordsaudkaric\Purephp\Session\Session;

class App
{
    private function __construct()
    {
    }

    public static function run()
    {
        /** Handle the Errors Whoops is an error handler framework for PHP */
        Whoops::handle();

        /** Start Session */
        Session::start();

        /** Handle the request */
        Request::handle();

        /** Require all routes directory */
        File::require_directory('routes');

        /** Handle the route */
        $data = Route::handle();

        Response::output($data);

    }
}