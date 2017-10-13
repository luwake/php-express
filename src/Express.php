<?php
namespace Luwake;

class Express extends Router
{

    public $engines = [];

    public $request;

    public $response;

    public function __construct($mountpath = '', $locals = [])
    {
        parent::__construct($mountpath, $locals);
        
        $env = getenv('APP_ENV') || 'development';
        
        $this->enable('x-powered-by');
        $this->set('etag', 'weak');
        $this->set('env', $env);
        $this->set('query parser', 'extended');
        $this->set('subdomain offset', 2);
        $this->set('trust proxy', false);
        
        $this->set('view', View::class);
        $this->set('views', explode(',', 'views'));
        $this->set('jsonp callback name', 'callback');
        
        if ($env === 'production') {
            $this->enable('view cache');
        }
    }

    public function get($path)
    {
        if (func_num_args() === 1 && ! is_callable($path)) {
            return $this->set($path);
        }
        $this->map('GET', func_get_args());
        return $this;
    }

    public function set($setting, $val = null)
    {
        if (func_num_args() === 1) {
            return isset($this->locals[$setting]) ? $this->locals[$setting] : true;
        }
        if ($this->enabled($setting)) {
            $this->locals[$setting] = $val;
        }
        return $this;
    }

    public function enabled($setting)
    {
        return $this->set($setting) !== false ? true : false;
    }

    public function disabled($setting)
    {
        return $this->set($setting) === false ? true : false;
    }

    public function enable($setting)
    {
        $this->set($setting, true);
        return $this;
    }

    public function disable($setting)
    {
        $this->set($setting, false);
        return $this;
    }

    public function engine($ext, $fn)
    {
        if (is_callable($fn)) {
            $extension = substr($ext, 0, 1) !== '.' ? '.' + $ext : $ext;
            $this->engines[$extension] = $fn;
        }
        return $this;
    }

    public function render($name, $options, $callback)
    {
        return (new View($name, $this->locals))->render($options, $callback);
    }

    public function listen()
    {
        $fn = $this;
        try {
            $res = $fn($this->request(), $this->response(), $this->done());
            if ($res instanceof Response) {
                return $res->send();
            }
            return $this->serialize($res);
        } catch (\Exception $err) {
            return $this->exception($err);
        }
    }

    public function request()
    {
        if (! $this->request) {
            $this->request = new Request($this);
        }
        return $this->request;
    }

    public function response()
    {
        if (! $this->response) {
            $this->response = new Response($this);
        }
        return $this->response;
    }

    private function done()
    {
        return function ($req, $res) {
            return $res;
        };
    }

    private function serialize($res)
    {
        if (! is_string($res)) {
            $res = json_encode($res);
        }
        return $this->response()
            ->write($res)
            ->send();
    }

    private function exception(\Exception $err)
    {
        dump($err);
    }
}    