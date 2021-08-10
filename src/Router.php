<?php
namespace Luwake;

use Luwake\Router\Route;
use PetrGrishin\Pipe\Pipe;
use Luwake\Router\Layer;

class Router
{
    /**
     * @var Router
     */
    public $router = null;
    
    public $path = '';
    
    private $stack = [];

    public function __construct($path = '/')
    {
        $this->path = $path;
        
        $this->stack = [];
    }

    public function path()
    {
        return $this->path;
    }

    public function resolve($path = '')
    {
        if($this->router){
            return rtrim(str_replace('//', '/', $this->router->resolve($this->path) . '/' . trim($path, '/')), '/');
        }
        
        return rtrim(str_replace('//', '/', $this->path . '/' . trim($path, '/')), '/');
    }

    public function param($name, $fn)
    {
        if (! $name) {
            throw new \Exception('argument name is required');
        }
        
        if (! is_string($name)) {
            throw new \Exception('argument name must be a string');
        }
        
        if (! $fn) {
            throw new \Exception('argument fn is required');
        }
        
        if (! is_callable($fn)) {
            throw new \Exception('argument fn must be a function');
        }
        
        $this->params[$name][] = $fn;
        
        return $this;
    }

    public function handle(Request $req, Response $res, $next)
    {
        return Pipe::create($req, $res)->through($this->stack)->then($next);
    }

    public function use($handler)
    {
        $offset = 0;
        $path = '/';
        
        if (! is_callable($handler)) {
            $offset = 1;
            $path = $handler;
        }
        
        $fns = array_flatten(array_slice(func_get_args(), $offset));
        
        if (count($fns) === 0) {
            throw new \Exception('app.use() requires middleware functions');
        }
        
        foreach ($fns as $fn) {
            
            if($fn instanceof Router){
                $fn->router = $this;
                
                if($path && $path != '/'){
                    $fn->path = $path;
                }else{
                    $path = $fn->path;
                }
            }
            
            $layer = new Layer($path, [], $fn);
            
            $layer->router = $this;
            
            $this->stack[] = $layer;
        }
        
        return $this;
    }

    public function route($path)
    {
        $route = new Route($path);
        
        $route->router = $this;
        
        $layer = new Layer($path, [], $route);
        
        $layer->router = $this;
        
        $this->stack[] = $layer;
        
        return $route;
    }

    public function get($path)
    {
        $route = $this->route($path);
        
        $route->get(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function post($path)
    {
        $route = $this->route($path);
        
        $route->post(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function put($path)
    {
        $route = $this->route($path);
        
        $route->put(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function delete($path)
    {
        $route = $this->route($path);
        
        $route->delete(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function options($path)
    {
        $route = $this->route($path);
        
        $route->options(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function all($path)
    {
        $route = $this->route($path);
        
        $route->all(...array_slice(func_get_args(), 1));
        
        return $this;
    }
    
    public function __invoke(Request $req, Response $res, $next)
    {
        return $this->handle($req, $res, $next);
    }
}
