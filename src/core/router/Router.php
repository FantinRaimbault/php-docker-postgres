<?php

namespace App\Core\Router;


use Closure;
use Error;

class Router
{
    private String $url;
    private array $routes;
    private array $middlewares;

    public function __construct($url)
    {
        $this->url = $url;
        $this->routes = [];
        $this->middlewares = [];
    }

    public function get(String $path, array $middlewares = [], Closure $callback): Route
    {
        $route = new Route($path, $middlewares, $callback);
        $this->routes['GET'][] = $route;
        return $route;
    }

    public function post(String $path, array $middlewares = [], Closure $callback): Route
    {
        $route = new Route($path, $middlewares, $callback);
        $this->routes['POST'][] = $route;
        return $route;
    }

    public function use(String $path, array $routes)
    {
        foreach ($routes as $route) {
            $route->setPath(trim($path, '/') . '/' . $route->getPath());
        }
    }

    public function addMiddleware(Closure $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function run(): int
    {
        if (!$this->routes[$_SERVER['REQUEST_METHOD']]) {
            echo 'request method not found';
            return 0;
        }
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                $this->applyMiddlewares($route);
                return $route->call();
            }
        }
        throw new Error();
    }

    private function applyMiddlewares(Route $route)
    {
        foreach ($this->middlewares as $middleware) {
            $route->addMiddleware($middleware);
        }
    }
}
