<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Router;

use BadFunctionCallException;
use InvalidArgumentException;
use Lordsaudkaric\Purephp\Request\Request;
use Lordsaudkaric\Purephp\View\View;
use ReflectionException;

class Route
{
    private static array $routes = [];
    private static string $middleware = '';
    private static string $prefix = '';

    private function __construct()
    {
    }

    public static function get(string $path, mixed $callback): void
    {
        self::add('get', $path, $callback);
    }

    public static function post(string $path, mixed $callback): void
    {
        self::add('post', $path, $callback);
    }

    public static function delete(string $path, mixed $callback): void
    {
        self::add('delete', $path, $callback);
    }

    public static function put(string $path, mixed $callback): void
    {
        self::add('put', $path, $callback);
    }

    public static function patch(string $path, mixed $callback): void
    {
        self::add('patch', $path, $callback);
    }

    public static function prefix(string $prefix, mixed $callback): void
    {
        $parent_prefix = self::$prefix;
        self::$prefix .= '/' . trim($prefix, '/');
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new BadFunctionCallException('Please provide an valid callback!');
        }
        self::$prefix = $parent_prefix;
    }

    public static function middleware(string $middleware, mixed $callback): void
    {
        $parent_middleware = self::$middleware;
        self::$middleware .= '|' . trim($middleware, '|');
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new BadFunctionCallException('Please provide an valid callback!');
        }
        self::$middleware = $parent_middleware;
    }

    public static function handle()
    {
        $uri = Request::url();
        $method = Request::post('__METHOD') ?? Request::method();

        foreach (self::$routes as $route) {
            $matched = true;
            $route['path'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['path']);
            $route['path'] = '#^' . $route['path'] . '$#';

            if (preg_match($route['path'], $uri, $matches)) {
                array_shift($matches);
                $params = array_values($matches);

                foreach ($params as $param) {
                    if (strpos($param, '/')) {
                        $matched = false;
                    }
                }

                if ($route['method'] !== strtolower($method)) {
                    $matched = false;
                }

                if ($matched == true) {
                    return self::invoke($route, $params);
                }
            }
        }

        return View::render('errors.404');
    }

    private static function invoke(mixed $route, array $params = [])
    {
        self::executeMiddleware($route);
        $callback = $route['callback'];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);

        } elseif (str_contains($callback, '@')) {
            list($controller, $method) = explode('@', $callback);
            $controller = 'App\Controllers\\' . $controller . 'Controller';
            if (class_exists($controller)) {
                $object = new $controller();
                if (method_exists($object, $method)) {
                    return call_user_func_array([$object, $method], $params);
                } else {
                    throw new BadFunctionCallException("Method: '{$method}' does not exists on class: '{$controller}'!");
                }
            } else {
                throw new ReflectionException("Class {$controller} does not exists!");
            }
        } else {
            throw new InvalidArgumentException('Pleas provide valid callback function!');
        }
    }

    private static function executeMiddleware(mixed $route)
    {
        $middlewares = explode('|', $route['middleware']);
        foreach ($middlewares as $middleware) {
            if (!empty($middleware)) {
                $middleware = 'App\Middlewares\\' . $middleware . 'Middleware';
                if (class_exists($middleware)) {
                    $object = new $middleware();
                    call_user_func_array([$object, 'handle'], []);
                } else {
                    throw new ReflectionException("Class {$middleware} does not exists!");
                }
            }
        }
    }

    private static function add(string $method, string $path, mixed $callback): void
    {
        $path = rtrim(self::$prefix . '/' . trim($path, '/'), '/');
        $path = $path ?: '/';

        self::$routes[] = [
            'path' => $path,
            'callback' => $callback,
            'method' => $method,
            'middleware' => self::$middleware
        ];
    }


}