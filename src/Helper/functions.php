<?php
/*
if (!function_exists('xxxx')) {
    function xxxx()
    {

    }
}
*/

if (!function_exists('auth')) {
    function auth(string $table)
    {
        $auth = Lordsaudkaric\Purephp\Session\Session::get($table) ?: Lordsaudkaric\Purephp\Cookie\Cookie::get($table);

        return Lordsaudkaric\Purephp\Database\Database::table($table)->where('id', '=', $auth)->first();
    }
}

if (!function_exists('session')) {
    function session(string $key)
    {
        return Lordsaudkaric\Purephp\Session\Session::get($key);
    }
}

if (!function_exists('flash')) {
    function flash(string $key)
    {
        return Lordsaudkaric\Purephp\Session\Session::flash($key);
    }
}

if (!function_exists('session')) {
    function session(string $key)
    {
        return Lordsaudkaric\Purephp\Session\Session::get($key);
    }
}

if (!function_exists('asset')) {
    function asset(string $path)
    {
        return Lordsaudkaric\Purephp\Url\Url::path('asset/' . $path);
    }
}

if (!function_exists('url')) {
    function url(string $path)
    {
        return Lordsaudkaric\Purephp\Url\Url::path($path);
    }
}

if (!function_exists('redirect')) {
    function previous()
    {
        return Lordsaudkaric\Purephp\Url\Url::previous();
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path)
    {
        return Lordsaudkaric\Purephp\Url\Url::redirect($path);
    }
}


if (!function_exists('request')) {
    function request(string $key)
    {
        return Lordsaudkaric\Purephp\Request\Request::value($key);
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [], $type = true)
    {
        return Lordsaudkaric\Purephp\View\View::render($view, $data, $type);
    }
}

if (!function_exists('dd')) {
    function dd($stuff, $die = true)
    {
        echo '<pre> 
            <div style="background-color: black; color: forestgreen; 
            margin: 0; padding: 15px; font-size: 20px; ">';
        if (is_string($stuff)) {
            echo $stuff;
        } else {
            print_r($stuff);
        }
        echo '</div>
        </pre>';
        if ($die) die();

    }
}