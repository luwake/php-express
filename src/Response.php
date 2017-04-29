<?php
namespace Luwake;

class Response extends \Brick\Http\Response
{
    protected $extends = [];
    
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
    
    public function json($body)
    {
        $this->setContent(json_encode($body));
        
        return $this;
    }
}
