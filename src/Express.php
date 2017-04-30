<?php
namespace Luwake;

use Evenement\EventEmitterTrait;
use Luwake\Interfaces\ExpressInterface;

class Express extends Router implements ExpressInterface
{
    use EventEmitterTrait;

    public static function Router($mountpath = '')
    {
        return new Router($mountpath);
    }

    public static function Request(Express $app)
    {
        return new Request($app);
    }

    public static function Response(Express $app)
    {
        return new Response($app);
    }

    public static function _static($callback, $options = [])
    {
        return function ($req, $res, $next) use ($callback, $options) {
            return $callback($req, $res, $next, $options);
        };
    }

    public $locals = array(
        'case sensitive routing' => false,
        'env' => 'development',
        'etag' => false,
        'jsonp callback name' => 'callback',
        'json replacer' => null,
        'json spaces' => false,
        'query parser' => 'simple',
        'strict routing' => false,
        'subdomain offset' => 2,
        'trust proxy' => false,
        'views' => false,
        'view cache' => true,
        'view engine' => 'php',
        'x-powered-by' => true
    );

    protected $disabled = array();

    protected $engines = [];

    public function __construct($mountpath = '')
    {
        parent::__construct($mountpath);
        
        $this->engine('php', function ($path, $locals) {
            extract($locals);
            ob_start();
            require $path;
            $conent = ob_get_contents();
            ob_clean();
            return $conent;
        });
    }

    public function disable($name)
    {
        $this->disabled[] = $name;
        
        return $this;
    }

    public function disabled($name)
    {
        return in_array($name, $this->disabled) ? true : false;
    }

    public function enable($name)
    {
        if ($this->disable($name)) {
            $this->disabled = array_merge(array_diff($this->disabled, array(
                $name
            )));
        }
        return $this;
    }

    public function enabled($name)
    {
        return ! in_array($name, $this->disabled) ? true : false;
    }

    public function engine($ext, $callback)
    {
        $this->engines[$ext] = $callback;
        
        return $this;
    }

    public function _get($name)
    {
        if ($this->disabled($name)) {
            return false;
        }
        return $this->locals[$name];
    }

    public function set($name, $value)
    {
        if ($this->enabled($name)) {
            $this->locals[$name] = $value;
        }
        return $this;
    }

    public function get($path = null, $callback = null)
    {
        if (func_num_args() == 1 && ! is_callable($path)) {
            return $this->_get($path);
        }
        
        $this->reslove(func_get_args(), 'GET');
        
        return $this;
    }

    public function render($view, $locals = [], $callback = null)
    {
        $engine = $this->engines[$this->_get('view engine')];
        
        $path = $this->_get('views') . '/' . ltrim($view, '/') . '.' . trim($this->_get('view engine'), '.');
        
        if (! $engine) {
            if ($callback) {
                return $callback($path, new \Exception("template engine [{$this->_get('view engine')}] not exist"));
            }
            return false;
        }
        
        if (! file_exists($path)) {
            if ($callback) {
                return $callback($path, new \Exception("template file [{$path}] not exist"));
            }
            return false;
        }
        return $engine($path, $locals);
    }

    public function listen()
    {
        $response = $this->handle(self::Request($this), self::Response($this));
        if ($response instanceof Response) {
            $response->end();
        }
    }
}
