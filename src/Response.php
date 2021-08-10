<?php
namespace Luwake;

use React\Http\Message\ServerRequest;
use React\Http\Message\Response as ServerResponse;
use function RingCentral\Psr7\mimetype_from_extension;

class Response
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

    private $headersSent;

    private $locals;

    public function __construct(Application $app)
    {
        $this->app = $app;
        
        $this->request = clone $app->request;
        
        $this->response = clone $app->response;
    }

    public function append($field, $value)
    {
        $this->response = $this->response->withAddedHeader($field, $value);
        
        return $this;
    }

    public function attachment($filename)
    {}

    public function cookie($name, $value, $options = [])
    {
        $this->response = $this->response->withAddedHeader('Cookie');
        
        return $this;
    }

    public function clearCookie($name, $options = [])
    {
        $this->response = $this->response->withHeader('Cookie', '-1');
        
        return $this;
    }

    public function download()
    {}

    public function end($data, $encoding = null)
    {
        if ($data) {
            $this->send($data);
        }
        
        $this->set('Content-Length', $this->response->getBody()
            ->getSize());
        
        return $this;
    }

    public function format($object)
    {}

    public function get($field)
    {
        return $this->response->getHeaderLine($field);
    }

    public function json($body = null)
    {
        return $this->type('json')->send($body);
    }

    public function jsonp($body = null)
    {
        $callback = isset($this->request->getQueryParams()['callback']) ? $this->request->getQueryParams()['callback'] : '';
        
        if ($callback) {
            
            $callback = preg_replace('/[^\[\]\w$.]/g', '', $callback);
            
            $body = json_encode($body);
            $body = preg_replace('/\u2028/g', '\\u2028', $body);
            $body = preg_replace('/\u2029/g', '\\u2029', $body);
            
            $body = '/**/ typeof ' + $callback + ' === \'function\' && ' + $callback + '(' + $body + ');';
            
            return $this->type('js')->send($body);
        }
        
        return $this->json($body);
    }

    public function links($links)
    {
        $link = $this->get('Link');
        
        foreach ($links as $res => $link) {
            $link[] = '<' + $link + '>; rel="' + $res + '"';
        }
        
        $this->set('Link', implode(',', $link));
        
        return $this;
    }

    public function location($path)
    {
        if($path == 'back'){
            $path = $this->request->getHeaderLine('Referrer') ? : '/';
        }
        return $this->set('Location', $path);
    }

    public function redirect($status, $path = null)
    {
        if (is_string($status)) {
            $path = $status;
            $status = 302;
        }
        $this->set('Location', $path)->status($status);
        
        return $this;
    }

    public function render($name, $options, $callback)
    {
        return $this->app->render($name, $options, $callback);
    }

    public function send($body)
    {
        switch (gettype($body)) {
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                if (! $this->get('Content-Type')) {
                    $this->type('html');
                }
                break;
            case 'array':
            case 'object':
                if (! $this->get('Content-Type')) {
                    $this->type('json');
                }
                $body = json_encode($body);
                break;
            case 'resource':
            case 'resource (closed)':
                if (! $this->get('Content-Type')) {
                    $this->type('txt');
                }
                $body = (string) $body;
                break;
            case 'NULL':
            case 'unknown type':
                $body = null;
        }
        
        if ($body) {
            $this->response->getBody()->write($body);
        }
        
        return $this;
    }

    public function sendFile($path, $options = [], $fn = null)
    {}

    public function sendStatus($statusCode)
    {
        $this->status($statusCode)->type('txt')->send($this->response->getReasonPhrase());
        
        return $this;
    }

    public function set($field, $value = null)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->set($key, $val);
            }
        } else {
            $this->response = $this->response->withHeader($field, $value);
        }
        
        return $this;
    }

    public function status($code)
    {
        $this->response = $this->response->withStatus($code);
        
        return $this;
    }

    public function type($type)
    {
        return $this->set('Content-Type', mimetype_from_extension($type))->charset();
    }
    
    public function charset($encoding = null)
    {
        $encoding = $encoding ? $encoding : 'utf-8';
        
        $type = $this->get('Content-Type');
        if ($type) {
            $this->set('Content-Type', $type . '; charset=' . $encoding);
        }
        
        return $this;
    }

    public function vary($field)
    {
        return $this->set('Vary', $field);
    }
    
    public function response()
    {
        return $this->response;
    }
}
