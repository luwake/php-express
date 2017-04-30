<?php
namespace Luwake\Interfaces;

interface ExpressInterface
{
    public function disable($name);
    
    public function disabled($name);
    
    public function enable($name);
    
    public function enabled($name);
    
    public function engine($ext, $callback);
    
    public function _get($name);
    
    public function set($name, $value);
    
    public function render($view, $locals = [], $callback = null);
    
    public function listen();
}