<?php
namespace Luwake;

use Luwake\Interfaces\ResponseInterface;

class Response extends \Brick\Http\Response implements ResponseInterface
{
    protected $app;
    
    protected $extends = [];
    
    public function __construct(Express $app)
    {
        $this->app = $app;
    }
    
    public function __set($name, $value)
    {
        if(property_exists($this, $name)){
            $this->$name = $value;
        }
        else{
            $this->extends[$name] = $value;
        }
    }
    
    public function __get($name)
    {
        if(property_exists($this, $name)){
            return $this->$name;
        }
        else{
            return isset($this->extends[$name])?$this->extends[$name]:null;
        }
    }
    
    public function extend($name, $callback)
    {
        $this->extends[$name] = $callback;
    }
    
    public function __call($method, $args)
    {
        $value = $this->__get($method);
    
        if ($value && is_callable($value)) {
            return call_user_func_array($value, $args);
        }
        return $value;
    }
    
    public function download($path, $filename = null, $fn = null)
    {
        // TODO Auto-generated method stub
        
    }

    public function end($data = null, $encoding = null)
    {
        if($data){
            $this->send($data);
        }
        
        parent::send();
    }

    public function format($object)
    {
        // TODO Auto-generated method stub
        
    }

    public function get($field)
    {
        return $this->getHeader($field);
    }

    public function set($field, $value = null)
    {
        $this->addHeader($field, $value);
        
        return $this;
    }

    public function json($body = null)
    {
        $this->setContent(json_encode($body));
        
        return $this;
    }

    public function jsonp($body = null)
    {
        $callback = $this->app->_get('jsonp callback name');
        
        $this->json($callback . '('. json_encode($body).')');
        
        return $this;
    }

    public function links($links)
    {
    }

    public function location($path)
    {
        $this->set('Location', $path);
        
        return $this;
    }

    public function redirect($status = null, $path)
    {
        $this->status($status)->location($path);
        
        return $this;
    }

    public function render($view, $locals = [], $callback = null)
    {
        $this->send($this->app->render($view, $locals, $callback));
        
        return $this;
    }

    public function send($body = null)
    {
        $this->setContent($body);
        
        return $this;
    }

    public function sendFile($path, $options = null, $fn = null)
    {
        // TODO Auto-generated method stub
        
    }

    public function sendStatus($statusCode)
    {
        $this->setStatusCode($statusCode)->send($this->getReasonPhrase());
        
        return $this;
    }

    public function status($code)
    {
        $this->setStatusCode($code);
        
        return $this;
    }

    public function type($type)
    {
        // TODO Auto-generated method stub
        
    }

    public function vary($field)
    {
        // TODO Auto-generated method stub
        
    }
    
    public function append($field, $value = null)
    {
        $this->headers[$field][] = $value;
        
        return $this;
    }

    public function attachment($filename = null)
    {
        // TODO Auto-generated method stub
        
    }
    
    public function cookie($name, $value, $options = [])
    {
        // TODO Auto-generated method stub
    }

    public function clearCookie($name, $options = [])
    {
        // TODO Auto-generated method stub
        
    }
}