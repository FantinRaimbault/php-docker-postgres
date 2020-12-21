<?php

namespace App\Core\Router;

use Closure;

class Route
{
    private string $path;
    private array $middlewares;
    private Closure $callback;
    private array $matches;

    public function __construct(string $path, array $middlewares, Closure $callback)
    {
        $this->path = trim($path, '/');
        $this->middlewares = $middlewares;
        $this->callback = $callback;
    }

    public function match(string $url): bool
    {
        $url = trim($url, '/');
        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);
        $regex = "#^$path$#i";
        if (!preg_match($regex, $url, $matches)) {
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    public function addMiddleware(Closure $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function call()
    {
        foreach ($this->middlewares as $middleware) {
            call_user_func_array($middleware, $this->matches);
        }
        return call_user_func_array($this->callback, $this->matches);
    }
}
