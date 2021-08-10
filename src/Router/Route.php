<?php
namespace Luwake\Router;

use PetrGrishin\Pipe\Pipe;
use Luwake\Request;
use Luwake\Response;
use Luwake\Router;

class Route
{
    /**
     * @var Router
     */
    public $router;
    
    public $path;
    
    private $stack = [];

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function path()
    {
        return $this->path;
    }
    
    public function resolve($path = '')
    {
        return rtrim(str_replace('//', '/', $this->router->resolve($this->path) . '/' . trim($path, '/')), '/');
    }

    public function get($handler)
    {
        return $this->map('get', func_get_args());
    }

    public function post($handler)
    {
        return $this->map('post', func_get_args());
    }

    public function put($handler)
    {
        return $this->map('put', func_get_args());
    }

    public function delete($handler)
    {
        return $this->map('delete', func_get_args());
    }

    public function options($handler)
    {
        return $this->map('options', func_get_args());
    }
    
    public function all($handler)
    {
        return $this->map('all', func_get_args());
    }
    
    private function map($method, $handler)
    {
        $layer = new Layer('/', [], $handler);
        
        $layer->route = $this;
        
        $layer->method = $method;
        
        $this->stack[] = $layer;
        
        return $layer;
    }

    public function handle(Request $req, Response $res, $next)
    {
        return Pipe::create($req, $res)->through($this->stack)->then($next);
    }

    public function __invoke(Request $req, Response $res, $next)
    {
        return $this->handle($req, $res, $next);
    }
}
