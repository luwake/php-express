<?php
namespace Luwake;

use Brick\Http\Exception\HttpBadRequestException;
use Brick\Http\UploadedFileMap;
use Brick\Http\MessageBodyResource;
use Luwake\Interfaces\RequestInterface;

class Request extends \Brick\Http\Request implements RequestInterface
{
    protected $app;
    
    protected $extends = [];
    
    public function __construct(Express $app)
    {
        $this->app = $app;
        
        $this->createFromGlobal();
    }
    
    public function createFromGlobal($trustProxy = false, $hostPortSource = self::PREFER_HTTP_HOST)
    {
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1') {
                $this->isSecure = true;
                $this->port = 443;
            }
        }
    
        $httpHost = null;
        $httpPort = null;
    
        $serverName = null;
        $serverPort = null;
    
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
            $pos = strrpos($host, ':');
    
            if ($pos === false) {
                $httpHost = $host;
                $httpPort = $this->port;
            } else {
                $httpHost = substr($host, 0, $pos);
                $httpPort = (int) substr($host, $pos + 1);
            }
        }
    
        if (isset($_SERVER['SERVER_NAME'])) {
            $serverName = $_SERVER['SERVER_NAME'];
        }
    
        if (isset($_SERVER['SERVER_PORT'])) {
            $serverPort = (int) $_SERVER['SERVER_PORT'];
        }
    
        $host = null;
        $port = null;
    
        switch ($hostPortSource) {
            case self::PREFER_HTTP_HOST:
                $host = ($httpHost !== null) ? $httpHost : $serverName;
                $port = ($httpPort !== null) ? $httpPort : $serverPort;
                break;
    
            case self::PREFER_SERVER_NAME:
                $host = ($serverName !== null) ? $serverName : $httpHost;
                $port = ($serverPort !== null) ? $serverPort : $httpPort;
                break;
    
            case self::ONLY_HTTP_HOST:
                $host = $httpHost;
                $port = $httpPort;
                break;
    
            case self::ONLY_SERVER_NAME:
                $host = $serverName;
                $port = $serverPort;
                break;
        }
    
        if ($host !== null) {
            $this->host = $host;
        }
    
        if ($port !== null) {
            $this->port = $port;
        }
    
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->method = $_SERVER['REQUEST_METHOD'];
        }
    
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->setRequestUri($_SERVER['REQUEST_URI']);
        }
    
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            if (preg_match('|^HTTP/(.+)$|', $_SERVER['SERVER_PROTOCOL'], $matches) !== 1) {
                throw new HttpBadRequestException('Invalid protocol: ' . $_SERVER['SERVER_PROTOCOL']);
            }
    
            $this->protocolVersion = $matches[1];
        }
    
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->clientIp = $_SERVER['REMOTE_ADDR'];
        }
    
        $this->headers = $this->getRequestHeaders();
    
        $this->post    = $_POST;
        $this->cookies = $_COOKIE;
        $this->files   = UploadedFileMap::createFromFilesGlobal($_FILES);
    
        if (isset($_SERVER['CONTENT_LENGTH']) || isset($_SERVER['HTTP_TRANSFER_ENCODING'])) {
            $this->body = new MessageBodyResource(fopen('php://input', 'rb'));
        }
    
        if ($trustProxy) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ips = preg_split('/,\s*/', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $this->clientIp = array_pop($ips);
            }
    
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                $this->host = $_SERVER['HTTP_X_FORWARDED_HOST'];
            }
    
            if (isset($_SERVER['HTTP_X_FORWARDED_PORT'])) {
                $this->port = (int) $_SERVER['HTTP_X_FORWARDED_PORT'];
            }
    
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                $this->isSecure = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
            }
        }
    }
    
    private function getRequestHeaders()
    {
        $headers = [];
    
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
    
            if ($requestHeaders) {
                foreach ($requestHeaders as $key => $value) {
                    $key = strtolower($key);
                    $headers[$key] = [$value];
                }
    
                return $headers;
            }
        }
    
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
            } elseif ($key !== 'CONTENT_TYPE' && $key !== 'CONTENT_LENGTH') {
                continue;
            }
    
            $key = strtolower(str_replace('_', '-', $key));
            $headers[$key] = [$value];
        }
    
        return $headers;
    }
    
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
    
    public function accepts($types)
    {
        // TODO Auto-generated method stub
    }

    public function acceptsCharsets($charset)
    {
        // TODO Auto-generated method stub
        
    }

    public function acceptsEncodings($encoding)
    {
        // TODO Auto-generated method stub
        
    }

    public function acceptsLanguages($lang)
    {
        // TODO Auto-generated method stub
        
    }

    public function get($field)
    {
        // TODO Auto-generated method stub
        
    }

    public function is($type)
    {
        // TODO Auto-generated method stub
        
    }

    public function param($name, $default)
    {
        // TODO Auto-generated method stub
        
    }

}
