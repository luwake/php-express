<?php
namespace Luwake;

class Request extends Base
{
    public $app;
    
    public $env;
    
    public $baseUrl;
    
    public $server;
    
    public $headers;
    
    public $attrs;
    
    public $get;
    
    public $body;
    
    public $cookies;
    
    public $session;
    
    public $files;
    
    public $fresh;
    
    public $hostname;
    
    public $method;
    
    public $ip;
    
    public $ips;
    
    public $originalUrl;
    
    public $referer;
    
    public $params;
    
    public $path;
    
    public $protocol = 'http';
    
    public $query;
    
    public $route;
    
    public $secure;
    
    public $signedCookies;
    
    public $stale;
    
    public $subdomains = array();
    
    public $xhr = false;
    
    public function __construct(Express $app)
    {
        $this->app = $app;
        
        $this->env = @$_ENV;
        
        $this->server = @$_SERVER;
        
        $this->get = @$_GET;
        
        $this->body = @$_POST;
        
        $this->params = @$_REQUEST;
        
        $this->cookies = @$_COOKIE;
        
        $this->session = @$_SESSION;
        
        $this->files = @$_FILES;
        
        $this->originalUrl = $this->server['REQUEST_URI'];
        
        $this->path = parse_url($this->originalUrl, PHP_URL_PATH);
        
        $this->hostname = $this->server['HTTP_HOST'];
        
        $this->method = $this->server['REQUEST_METHOD'];
        
        $this->ip = $this->server['REMOTE_ADDR'];
        
        $this->referer = @$this->server['HTTP_REFERER'];
        
        $this->headers = $this->headers();
        
        $this->xhr = (isset($this->server['HTTP_X_REQUESTED_WITH']) && $this->server['HTTP_X_REQUESTED_WITH'] == 'XmlHttpRequest')?true:false;
        
        $this->secure = ($this->server['REQUEST_SCHEME'] == 'https') || ($this->server['SERVER_PORT'] == '443');
    }
    
    public function accepts($types = array())
    {
        
    }

    public function acceptsCharses($charset)
    {
        
    }

    public function acceptsEncodings($encoding)
    {
        
    }

    public function acceptsLanguages($lang)
    {
        
    }

    public function get($key)
    {
        return $this->headers[$key];
    }
    
    public function headers()
    {
        if($this->headers){
            return $this->headers;
        }
        
        $headers = array();
        
        foreach ($this->server as $key=>$val){
            if(strpos($key, 'HTTP_') === 0){
                $name = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$name] = $val;
            }
        }
        
        return $headers;
    }

    public function header($key)
    {
        return $this->headers[$key];
    }

    public function is($type)
    {
        
    }

    public function param($name, $default = null)
    {
        return isset($this->params[$name])?$this->params[$name]:$default;
    }
}
