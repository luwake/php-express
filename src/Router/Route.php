<?php
namespace Luwake\Router;

use Luwake\Request;
use Luwake\Response;

class Route
{

    public $handle;

    public $method;

    public $path;

    public $keys = [];

    public $regexp;

    public $params = [];

    public function __construct($path, $method, $fn)
    {
        $this->path = $path ? $path : '/';
        $this->method = $method;
        $this->handle = $fn;
        $this->regexp = \PathToRegexp::convert($path, $this->keys);
    }

    public function match(Request $req)
    {
        $path = $req->path();
        
        $method = $req->method();
        
        if ($this->method == 'ALL' || $this->method == $method) {
            if ($path == null) {
                return true;
            }
            if (strpos($path, $this->path) === 0) {
                return true;
            }
            $match = \PathToRegexp::match($this->regexp, $path);
            if ($match) {
                $this->params = array_slice($match, 1);
                return true;
            }
        }
        return false;
    }

    public function __invoke(Request $req, Response $res, $next)
    {
        $fn = $this->handle;
        if ($this->match($req)) {
            return $fn($req, $res, $next);
        }
        return $next($req, $res);
    }
}