<?php
namespace Luwake\Traits;

trait ExtendTrait
{

    public $extends;

    public function extend($name, $handle)
    {
        $this->extends[$name] = $handle;
    }

    public function __call($method, $args)
    {
        $value = false;
        if (isset($this->extends[$method])) {
            $value = $this->extends[$method];
        }
        if (property_exists($this, $method)) {
            $value = $this->$method;
        }
        if ($value && is_callable($value)) {
            $value = \Closure::bind($value, $this);
            return call_user_func_array($value, $args);
        }
        return $value;
    }
}