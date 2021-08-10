<?php
namespace Luwake\Middlewares;

use Luwake\Request;
use Luwake\Response;

class Json
{

    private $options = [
        'inflate' => true,
        'limit' => '100kb',
        'reviver' => null,
        'strict' => true,
        'type' => 'application/json; charset=utf-8',
        'verify' => null
    ];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function __invoke(Request $req, Response $res, $next)
    {
        $result = $next($req, $res);
        
        if ($result instanceof Response) {
            return $result;
        }
        
        return $res->type('json')->send($result);
    }
}
