<?php
namespace Luwake;

class Request extends \Brick\Http\Request
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
}
