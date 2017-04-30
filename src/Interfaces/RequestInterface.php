<?php
namespace Luwake\Interfaces;

interface RequestInterface
{
    public function accepts($types);
    
    public function acceptsCharsets($charset);
    
    public function acceptsEncodings($encoding);
    
    public function acceptsLanguages($lang);
    
    public function get($field);
    
    public function is($type);
    
    public function param($name ,$default);
}