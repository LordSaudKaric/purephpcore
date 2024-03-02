<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\View;

use Exception;
use Jenssegers\Blade\Blade;
use Lordsaudkaric\Purephp\File\File;
use Lordsaudkaric\Purephp\Session\Session;

class View
{
    public function __construct()
    {
    }

    public static function render(string $view, array $data = [], $type = true)
    {
        $data = array_merge($data, ['errors' => Session::flash('errors'), 'old' => Session::flash('old')]);
        if (!$type) {
            return self::viewRender($view, $data);
        }
        return self::bladeRender($view, $data);
    }

    private static function viewRender(string $view, array $data = [])
    {
        $path = 'views' . DS . str_replace(['/', '@', '.', '#'], DS, $view) . '.php';

        if (!File::exists($path)) {
            throw new Exception("The view '{$path}' does not exists!");
        }

        ob_start();
        extract($data);
        include_once File::path($path);
        $content = ob_get_contents();
        ob_get_clean();
        return $content;
    }

    private static function bladeRender(string $view, array $data)
    {
        $blade = new Blade(File::path('views'), File::path('storage/cache'));

        return $blade->make($view, $data)->render();
    }
}