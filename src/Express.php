<?php
namespace Luwake;

use Luwake\Middlewares\Json;
use Luwake\Middlewares\Statics;
use Luwake\Middlewares\Urlencoded;

class Express
{
    public static function Application($mountpath = '/')
    {
        return Application::getInstance($mountpath);
    }
    
    public static function json($options = [])
    {
        return new Json($options);
    }
    
    public static function static($root, $options = [])
    {
        return new Statics($root, $options);
    }
    
    public static function Router($path = '/')
    {
        return new Router($path);
    }
    
    public static function urlencoded()
    {
        return new Urlencoded();
    }
}
