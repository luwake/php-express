<?php
namespace Luwake;

use React\Http\Message\ServerRequest;
use React\Http\Message\Response as ServerResponse;

/**
 * 
 * @author Administrator
 * @property string $baseUrl
 * @property array $body
 * @property array $cookies
 * @property boolean $fresh
 * @property string $host
 * @property string $hostname
 * @property string $ip
 * @property array $ips
 * @property string $method
 * @property string $originalUrl
 * @property string $path
 * @property string $protocol
 * @property array $query
 * @property boolean $secure
 * @property array $signedCookies
 * @property boolean $stale
 * @property string $subdomains
 * @property boolean $xhr
 */
class Request
{

    /**
     * Application
     *
     * @var Application
     */
    private $app;

    /**
     * PSR-7 Request
     *
     * @var ServerRequest
     */
    private $request;

    /**
     * PSR-7 Response
     *
     * @var ServerResponse
     */
    private $response;

    public $route;

    public $params = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        
        $this->request = clone $app->request;
        
        $this->response = clone $app->response;
    }

    public function accepts()
    {}

    public function acceptsCharsets()
    {}

    public function acceptsEncodings()
    {}

    public function acceptsLanguages()
    {}

    public function get()
    {}

    public function is()
    {}

    public function param()
    {}

    public function range()
    {}

    public function __get($name)
    {
        switch ($name) {
            case 'baseUrl':
                return '/';
            case 'body':
                return $this->request->getParsedBody();
            case 'cookies':
                return $this->request->getCookieParams();
            case 'fresh':
                break;
            case 'host':
                break;
            case 'hostname':
                break;
            case 'ip':
                break;
            case 'ips':
                break;
            case 'method':
                return $this->request->getMethod();
            case 'originalUrl':
                break;
            case 'path':
                return $this->request->getUri()->getPath();
            case 'protocol':
                if ($this->request->getHeaderLine('HTTPS') == 'on') {
                    return 'https';
                }
                if ($this->request->getHeaderLine('X_FORWARDED_PROTO') == 'https') {
                    return 'https';
                }
                return 'http';
            case 'query':
                return $this->request->getQueryParams();
            case 'secure':
                if ($this->request->getHeaderLine('HTTPS') == 'on') {
                    return true;
                }
                if ($this->request->getHeaderLine('X_FORWARDED_PROTO') == 'https') {
                    return true;
                }
                return false;
            case 'signedCookies':
                break;
            case 'stale':
                break;
            case 'subdomains':
                break;
            case 'xhr':
                break;
        }
        ;
        return null;
    }
}
