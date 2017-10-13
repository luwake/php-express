<?php
namespace Luwake;

class View
{

    private $defaultEngine;

    private $ext;

    private $name;

    private $root;

    private $engine;

    private $path;

    public function __construct($name, $options)
    {
        $opts = $options ?  : [];
        
        $this->defaultEngine = $opts['defaultEngine'];
        $this->ext = pathinfo($name, PATHINFO_EXTENSION);
        $this->name = $name;
        $this->root = $opts['root'];
        
        if (! $this->ext && ! $this->defaultEngine) {
            throw new \Exception('No default engine was specified and no extension was provided.');
        }
        
        $fileName = $name;
        
        if (! $this->ext) {
            $this->ext = $this->defaultEngine[0] !== '.' ? '.' + $this->defaultEngine : $this->defaultEngine;
            
            $fileName += $this->ext;
        }
        
        if (! $opts['engines'][$this->ext]) {
            
            $mod = substr($this->ext, 1);
            
            $fn = [
                "Luwake\\Views\\$mod",
                '__express'
            ];
            
            if (! is_callable($fn)) {
                throw new \Exception('Module "' + $mod + '" does not provide a view engine.');
            }
            
            $opts['engines'][$this->ext] = $fn;
        }
        
        $this->engine = $opts['engines'][$this->ext];
        
        $this->path = $this->lookup($fileName);
    }

    public function lookup($name)
    {
        $path = false;
        
        $roots = array_merge([], $this->root);
        
        foreach ($roots as $root) {
            
            $loc = $this->resolve($root, $name);
            
            $dir = dirname($loc);
            
            $file = basename($loc);
            
            $path = $this->resolve($dir, $file);
        }
        
        return $path;
    }

    public function render($options, $callback)
    {
        $engine = $this->engine;
        
        return $engine($this->path, $options, $callback);
    }

    public function resolve($dir, $file)
    {
        $ext = $this->ext;
        
        $path = implode('/', array(
            $dir,
            $file
        ));
        
        if (is_file($path)) {
            return $path;
        }
        
        $path = implode('/', array(
            $dir,
            basename($file, $ext),
            'index' . $ext
        ));
        
        if (is_file($path)) {
            return $path;
        }
    }
}