<?php
namespace Luwake\Router;

use Luwake\Request;
use Luwake\Response;

class Param
{

    private $handle;

    private $keys;

    private $params;

    private $path;

    public function __construct($keys, $fn)
    {
        $this->keys = $keys;
        $this->handle = $fn;
    }

    public function match(Request $req)
    {
        foreach ($this->keys as $key) {
            if (($value = $req->param($key)) !== null) {
                $this->params[$key] = $value;
            }
        }
        if (count($this->keys) == count($this->params)) {
            return true;
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