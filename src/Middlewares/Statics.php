<?php
namespace Luwake\Middlewares;

use Luwake\Request;
use Luwake\Response;

class Statics
{

    private $root = '';

    private $options = [
        'dotfiles' => 'ignore',
        'etag' => true,
        'extensions' => false,
        'fallthrough' => true,
        'immutable' => false,
        'index' => 'index.html',
        'lastModified' => true,
        'maxAge' => 0,
        'redirect' => true,
        'setHeaders' => null
    ];

    public function __construct($root, $options = [])
    {
        $this->root = $root;
        
        $this->options = array_merge($this->options, $options);
    }

    public function __invoke(Request $req, Response $res, $next)
    {
        $path = $req->path;
        
        if (strpos($path, '.') !== false) {
            if ($req->method === 'GET' || $req->method === 'HEAD') {
                $file = $this->root . $path;
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if (file_exists($file)) {
                    $res->send(file_get_contents($file));
                    return $res->type($ext);
                }
            }
            return $res->status(404);
        }
        
        return $next($req, $res);
    }
}
