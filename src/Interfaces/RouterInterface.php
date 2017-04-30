<?php
namespace Luwake\Interfaces;

interface RouterInterface
{
    public function get($path = null, $callback = null);
    
    public function post($path = null, $callback = null);
    
    public function put($path = null, $callback = null);
    
    public function delete($path = null, $callback = null);
    
    public function all($path = null, $callback = null);
    
    public function param($name, $callback);
    
    public function route($path = '');
    
    public function _use($path, $callback = null);
}