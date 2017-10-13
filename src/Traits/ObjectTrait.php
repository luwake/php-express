<?php
namespace Luwake\Traits;

trait ObjectTrait
{

    protected $data = [];

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            $this->data[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            return isset($this->data[$name]) ? $this->data[$name] : null;
        }
    }

    public function __isset($name)
    {
        if (property_exists($this, $name)) {
            return true;
        } else {
            return isset($this->data[$name]) ? true : false;
        }
    }

    public function __unset($name)
    {
        if (property_exists($this, $name)) {
            unset($this->$name);
        } else {
            if (isset($this->data[$name])) {
                unset($this->data[$name]);
            }
        }
    }

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->__unset($offset);
    }
}