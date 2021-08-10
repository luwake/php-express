<?php
namespace Luwake\Router;

use Luwake\Request;
use Luwake\Response;
use PetrGrishin\Pipe\Pipe;
use Luwake\Router;

class Layer
{
    private $path = null;

    private $options = [];

    private $handle = null;

    /**
     * @var Router
     */
    public $router = null;

    /**
     * @var Route
     */
    public $route = null;

    public $method = null;
    
    public function __construct($path, $options = [], $fn)
    {
        $this->path = $path;
        
        $this->options = $options;
        
        $this->handle = $fn;
    }

    public function handle(Request $req, Response $res, $next)
    {
        if ($this->method == null || $this->method == strtolower($req->method)) {
            $path = $this->path;
            
            if($path == '*' || $path == '/'){
                return $this->process($req, $res, $next);
            }
            
            if ($this->router) {
                $path = $this->router->resolve($this->path);
            }
            
            if ($this->route) {
                $path = $this->route->resolve($this->path);
            }
            
            $keys = [];
            $regexp = \PathToRegexp::convert($path, $keys);
            if ($keys) {
                $matches = \PathToRegexp::match($regexp, $req->path);
                if ($matches) {
                    $params = array_slice($matches, 1);
                    foreach ($keys as $i=>$key){
                        $params[$key['name']] = $params[$i];
                    }
                    $req->params = $params;
                    return $this->process($req, $res, $next);
                }
            } else {
                if (strpos($req->path, $path) === 0) {
                    return $this->process($req, $res, $next);
                }
            }
        }
        return $next($req, $res);
    }

    public function process(Request $req, Response $res, $next)
    {
        return Pipe::create($req, $res)->through($this->handle)->then($next);
    }

    public function __invoke(Request $req, Response $res, $next)
    {
        return $this->handle($req, $res, $next);
    }
}
