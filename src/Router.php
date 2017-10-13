<?php
namespace Luwake;

use Luwake\Router\Route;
use Luwake\Router\Param;
use Evenement\EventEmitterTrait;
use Evenement\EventEmitterInterface;
use Luwake\Traits\ObjectTrait;
use Luwake\Utils\Pipeline;

class Router implements \ArrayAccess, EventEmitterInterface
{
    use ObjectTrait,EventEmitterTrait;

    public $locals = [];

    public $mountpath = '';

    public $routes;

    public $route;

    public function __construct($mountpath = '', $locals = [])
    {
        $this->mountpath = $mountpath;
        $this->locals = $locals;
    }

    public function get($path)
    {
        $this->map('GET', func_get_args());
        return $this;
    }

    public function post($path)
    {
        $this->map('POST', func_get_args());
        return $this;
    }

    public function put($path)
    {
        $this->map('PUT', func_get_args());
        return $this;
    }

    public function delete($path)
    {
        $this->map('DELETE', func_get_args());
        return $this;
    }

    public function all($path)
    {
        $this->map('ALL', func_get_args());
        return $this;
    }

    private $methods = [
        'checkout',
        'connect',
        'copy',
        'delete',
        'get',
        'head',
        'lock',
        'merge',
        'mkactivity',
        'mkcol',
        'move',
        'msearch',
        'notify',
        'options',
        'patch',
        'post',
        'propfind',
        'proppatch',
        'purge',
        'put',
        'report',
        'search',
        'subscribe',
        'trace',
        'unlock',
        'unsubscribe'
    ];

    public function _use($fn)
    {
        $this->map('ALL', func_get_args());
        return $this;
    }

    public function __call($method, $args)
    {
        if ($method == 'use') {
            return call_user_func_array([
                $this,
                '_use'
            ], $args);
        }
        if (in_array($method, $this->methods)) {
            $this->map(strtoupper($method), $args);
        }
        
        return $this;
    }

    protected function map($method, $args)
    {
        if (count($args) !== 0) {
            $path = $args[0];
            $callbacks = [];
            if (is_callable($path)) {
                $callbacks = $args;
                $path = '/';
            } else {
                $callbacks = array_slice($args, 1);
            }
            if ($callbacks && is_array($callbacks)) {
                foreach ($callbacks as $fn) {
                    $this->routes[] = [
                        Route::class,
                        $this->resolve($path),
                        $method,
                        $fn
                    ];
                }
            }
        }
    }

    public function param($name)
    {
        $args = func_get_args();
        if (count($args) !== 0) {
            $keys = $args[0];
            $callbacks = [];
            if (is_callable($keys)) {
                $callbacks = $args;
                $keys = [];
            } else {
                $callbacks = array_slice($args, 1);
            }
            if ($callbacks && is_array($callbacks)) {
                foreach ($callbacks as $fn) {
                    $this->routes[] = [
                        Param::class,
                        $keys,
                        $fn
                    ];
                }
            }
        }
        
        return $this;
    }

    public function route($path)
    {
        $router = new Router($this->resolve($path), $this->locals);
        $this->routes[] = $router;
        return $router;
    }

    public function path()
    {
        return $this->route->path;
    }

    protected function resolve($path)
    {
        return $this->mountpath . '/' . trim($path, '/');
    }

    public function __invoke($req, $res, $next)
    {
        return Pipeline::create($req, $res)->through($this->routes)->then($next);
    }
}