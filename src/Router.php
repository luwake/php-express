<?php
namespace Luwake;

use PetrGrishin\Pipe\Pipe;

class Router
{
    public $mountpath;
    
    protected $routes = array();
    
    public function __construct($mountpath = '')
    {
        if($mountpath){
            $this->mount($mountpath);
        }
    }
    
    public function get($path = null, $callback = null)
    {
        $this->reslove(func_get_args(), 'GET');
        
        return $this;
    }

    public function post($path = null, $callback = null)
    {
        $this->reslove(func_get_args(), 'POST');
        
        return $this;
    }

    public function put($path = null, $callback = null)
    {
        $this->reslove(func_get_args(), 'PUT');
        
        return $this;
    }

    public function delete($path = null, $callback = null)
    {
        $this->reslove(func_get_args(), 'DELETE');
        
        return $this;
    }

    public function all($path = null, $callback = null)
    {
        $this->reslove(func_get_args(), 'ANY');
        
        return $this;
    }
    
    public function map($methods, $path = null, $callback = null)
    {
        $this->reslove(func_get_args());
    
        return $this;
    }
    
    public function param($name, $callback)
    {
        
    }

    public function path()
    {
        return $this->mountpath;
    }
    
    public function mount($mountpath = '')
    {
        $this->mountpath = rtrim($mountpath, '/');
        
        return $this;
    }

    public function route($path = '')
    {
        $router = new Router($path);
        
        $this->_use($path, $router);
        
        return $router;
    }

    public function _use($path, $callback = null)
    {
        $this->reslove(func_get_args(), 'ANY');
        
        return $this;
    }
    
    public function __call($method, $args)
    {
        if($method == 'use'){
            $this->reslove($args, 'ANY');
        }
        
        return $this;
    }
    
    protected function reslove($args = [], $unshift = null)
    {
        if ($unshift) {
            array_unshift($args, $unshift);
        }
    
        if (0 == count($args)) {
            return false;
        }
    
        if (1 == count($args)) {
            $method = 'ANY';
            $path = '/';
            $handles = $args;
        } elseif (2 == count($args)) {
            $method = array_shift($args);
            $path = '/';
            $handles = $args;
        } else {
            $method = array_shift($args);
            $path = array_shift($args);
            $handles = $args;
        }
        foreach ($handles as $handle){
            $this->addHandle($method, $path, $handle);
        }
    }
    
    protected function addHandle($method, $path, $handle)
    {
        if ($handle instanceof Router) {
            return $this->addRouter($method, $path, $handle);
        }
    
        if(!$handle instanceof \Closure){
            $handle = function($request, $response, $next)use($handle){
                $handle = $this->generate($handle);
                if(is_callable($handle)){
                    return $handle($request, $response, $next);
                }
                return $next($request, $response);
            };
        }
    
        return $this->addRoute($method, $path, $handle);
    }
    
    protected function generate($handle)
    {
        if(is_string($handle)){
            if(function_exists($handle)){
                return $handle;
            }
            elseif(class_exists($handle)){
                $handle = new $handle();
            }
            elseif (strpos($handle, '@') !== false) {
                $handle = explode('@', $handle, 2);
            }
        }
    
        if(is_array($handle)){
    
            list($class, $method) = $handle;
    
            if(is_object($class)){
                return $handle;
            }
    
            if ((new \ReflectionMethod($class, $method))->isStatic()) {
                $handle = [$class, $method];
            } else {
                $handle = [new $class, $method];
            }
        }
        return $handle;
    }
    
    protected function addRouter($method, $path, Router $router)
    {
        $this->addRoute($method, $path, function ($request, $response, $next) use($path, $router) {
            return $router->mount($this->mountpath . rtrim($path, '/'))->handle($request, $response, $next);
        });
    }
    
    protected function addRoute($method, $path, callable $callback)
    {
        $this->routes[] = function ($request, $response, $next) use($method, $path, $callback) {
            if ($this->matcher($request, $method, $path)) {
                return $callback($request, $response, $next);
            }
            return $next($request, $response);
        };
    }
    
    protected function matcher(Request &$request, $method, $path)
    {
        $request->attrs = [];
        
        if ($method == 'ANY' || (is_string($method) && $method == $request->method) || (is_array($method) && in_array($request->method, $method))) {
    
            $route = $this->mountpath . rtrim($path, '/');
    
            $request->attrs['_route'] = $route;
            
            if (strpos($request->path, $route) === 0) {
                $request->route = array(
                    'path'=>$path,
                    'stack'=>array(
                        'params'=>[],
                        'path'=> $request->path,
                        'keys'=> [],
                        'regexp'=> null,
                        'method'=> $request->method
                    ),
                    'methods'=>$method
                );
                return true;
            }
            
            $keys = array();
    
            $preg = \PathToRegexp::convert($route, $keys, array(
                'end' => false
            ));
    
            $matches = \PathToRegexp::match($preg, $request->path);
    
            if (null != $matches) {
                
                $request->attrs['_matche'] = $matches[0];
                
                $matches = array_slice($matches, 1);
    
                foreach ($keys as $i => $key) {
                    
                    $request->attrs[$key['name']] = $matches[$i];
                }

                $request->route = array(
                    'path'=>$matches[0],
                    'stack'=>array(
                        'params'=>$matches,
                        'path'=> $request->path,
                        'keys'=> $keys,
                        'regexp'=> $preg,
                        'method'=> $request->method
                    ),
                    'methods'=>$method
                );

                return true;
            }
        }
        return false;
    }
    
    public function handle(Request $request, Response $response, $root = null)
    {
        return Pipe::create($request, $response)
            ->through($this->routes)
            ->through(function ($request, $response, $next) use($root) {
                if ($root) {
                    return $root($request, $response);
                }
                return $next($request, $response);
            })
            ->then(function ($request, $response) {
                return $response;
            });
    }
}