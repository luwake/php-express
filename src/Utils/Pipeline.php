<?php
namespace Luwake\Utils;

use PetrGrishin\Pipe\Pipe;

class Pipeline extends Pipe
{

    protected $method = 'handle';

    public function via($method)
    {
        $this->method = $method;
        return $this;
    }

    protected function getSlice()
    {
        return function ($stack, $pipe) {
            return function () use($stack, $pipe) {
                $passable = func_get_args();
                $passable[] = $stack;
                if (is_callable($pipe)) {
                    return $pipe(...$passable);
                }
                if (! is_object($pipe)) {
                    if (is_array($pipe)) {
                        $class = array_shift($pipe);
                        $reflectionClass = new \ReflectionClass($class);
                        $object = $reflectionClass->newInstanceArgs($pipe);
                    } else {
                        $object = new $pipe();
                    }
                } else {
                    $object = $pipe;
                }
                return method_exists($object, $this->method) ? $object->{$this->method}(...$passable) : $object(...$passable);
            };
        };
    }
}