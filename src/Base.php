<?php
namespace Luwake;

class Base implements \ArrayAccess
{
    protected $extends = [];
    
    public function offsetExists($offset)
    {
        return isset($this->extends[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset)?$this->extends[$offset]:null;
    }

    public function offsetSet($offset, $value)
    {
        $this->extends[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->extends[$offset]);
    }
    
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }
    
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
    
    public function extend($name, $callback)
    {
        $this->extends[$name] = $callback;
    }
    
    public function __call($method, $args)
    {
        $value = $this->offsetGet($method);
        
        if($value){
            if(is_callable($value)){
                return call_user_func_array($value, $args);
            }
        }
        return $value;
    }
}
