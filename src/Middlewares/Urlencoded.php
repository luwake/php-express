<?php
namespace Luwake\Middlewares;

use Luwake\Request;
use Luwake\Response;

class Urlencoded
{
    private $options = [
        'extended' => true,
        'inflate' => true,
        'limit' => '100kb',
        'parameterLimit' => '1000',
        'type' => 'application/x-www-form-urlencoded',
        'verify' => null,
    ];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }
    
    public function __invoke(Request $req, Response $res, $next)
    {
        return $next($req, $res);
    }
}
