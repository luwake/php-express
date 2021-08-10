<?php
namespace Luwake\Middlewares;

use Luwake\Request;
use Luwake\Response;
use Gumlet\ImageResize;

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
                    switch ($ext) {
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                            $image = new ImageResize($file);
                            
                            $size = isset($req->query['size']) ? $req->query['size'] : null;
                            if ($size) {
                                list ($width, $height) = explode('x', $size);
                                $image->crop($width, $height, true, ImageResize::CROPCENTER);
                            }
                            
                            $res->send($image->getImageAsString());
                            break;
                        case 'gif':
                        default:
                            $res->send(file_get_contents($file));
                            break;
                    }
                    return $res->type($ext);
                }
            }
            return $res->status(404);
        }
        
        return $next($req, $res);
    }
}
