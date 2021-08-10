<?php
namespace Luwake;

use React\Http\Message\ServerRequest;
use React\Http\Message\Response as ServerResponse;
use Evenement\EventEmitterTrait;
use Luwake\Router\Route;
use Evenement\EventEmitter;

class Application
{
    use EventEmitterTrait;
    
    private static $instance;
    
    private $settings = [];
    
    private $mountpath = '/';
    
    /**
     * @var Application
     */
    private $parent;

    /**
     * @var Router
     */
    private $router;
    
    /**
     * @var EventEmitter
     */
    private $event;
    
    /**
     * @var ServerRequest
     */
    public $request;
    
    /**
     * @var ServerResponse
     */
    public $response;
    
    public static function getInstance($mountpath = '/')
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($mountpath);
        }
        return self::$instance;
    }
    
    private function __construct($mountpath = '/')
    {
        $this->mountpath = $mountpath;
        
        $this->router = new Router($this->mountpath);
        
        $this->event = new EventEmitter();
    }
    
    private function __clone(){}
    
    public function handle(Request $req, Response $res, $callback)
    {
        $done = $callback ? : function($req, $res){
            return $res->status(200);
        };
        
        return $this->router->handle($req, $res, $done);
    }
    
    public function path()
    {
        return $this->mountpath;
    }
    
    /* Event Methods START */
    public function on($event, $listener)
    {
        $this->event->on($event, $listener);
        
        return $this;
    }
    
    public function once($event, $listener)
    {
        $this->event->once($event, $listener);
        
        return $this;
    }
    
    public function emit($event, $args = [])
    {
        $this->emit($event, $args);
        
        return $this;
    }
    /* Event Methods END */
    
    /* Settings Methods START */
    public function set($setting, $val = null)
    {
        if (func_num_args() === 1) {
            return isset($this->settings[$setting]) ? $this->settings[$setting] : null;
        }
        
        $this->settings[$setting] = $val;
        
        return $this;
    }
    
    public function enabled($setting)
    {
        return $this->set($setting) == true;
    }
    
    public function disabled($setting)
    {
        return $this->set($setting) == false;
    }
    
    public function enable($setting)
    {
        return $this->set($setting, true);
    }
    
    public function disable($setting)
    {
        return $this->set($setting, false);
    }
    /* Settings Methods END */
    
    /* Router Methods START */
    public function use($fn)
    {
        return $this->router->use(...func_get_args());
    }
    
    public function route($path)
    {
        return $this->router->route($path);
    }
    
    public function param($name, $fn)
    {
        if (is_array($name)) {
            foreach ($name as $val){
                $this->param($val, $fn);
            }
            
            return $this;
        }
        
        $this->router->param($name, $fn);
        
        return $this;
    }
    
    public function get($path)
    {
        if (func_num_args() === 1) {
            return $this->set($path);
        }
        
        $route = $this->route($path);
        
        $route->get(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function post($path)
    {
        $route = $this->route($path);
        
        $route->post(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function put($path)
    {
        $route = $this->route($path);
        
        $route->put(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function delete($path)
    {
        $route = $this->route($path);
        
        $route->delete(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function options($path)
    {
        $route = $this->route($path);
        
        $route->options(...array_slice(func_get_args(), 1));
        
        return $this;
    }

    public function all($path)
    {
        $route = $this->route($path);
        
        $route->all(...array_slice(func_get_args(), 1));
        
        return $this;
    }
    /* Router Methods END */
    
    /* View Methods START */
    public function engine($ext, $fn)
    {
        
    }

    public function render($name, $options, $callback)
    {
        
    }
    /* View Methods END */
    
    public function listen($port, $host = '127.0.0.1', $backlog = null, $callback = null)
    {
        $server = new \React\Http\Server($this);
        $socket = new \React\Socket\Server($host . ':' . $port);
        $server->listen($socket);
    }
    
    public function __invoke(ServerRequest $req)
    {
        $this->request = $req;
        
        $this->response = new ServerResponse();
        
        try {
            $res = $this->handle(new Request($this), new Response($this), null);
        }catch (\Exception $e){
            return $this->response->withStatus(500, $e->getMessage());
        }
        
        return $res->response();
    }
}
