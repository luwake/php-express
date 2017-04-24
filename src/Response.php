<?php
namespace Luwake;

class Response extends Base
{
    public $app;
    
    public $headersSent = false;
    
    public $status = '200';
    
    public $body;
    
    public $locals;
    
    public $protocol = '1.1';
    
    protected $headers = array();
    
    protected $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated to 306 => '(Unused)'
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];
    
    public function __construct(Express $app)
    {
        $this->app = $app;
    }
    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::append()
     */
    public function append($field, $value = null)
    {
        // TODO Auto-generated method stub
        $this->headers[$field][] = $value;
        
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::attachment()
     */
    public function attachment($filename = null)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::cookie()
     */
    public function cookie($name, $value, $options = array())
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::clearCookie()
     */
    public function clearCookie($name, $options = array())
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::download()
     */
    public function download($path, $filename = null, $fn = null)
    {
        // TODO Auto-generated method stub
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::end()
     */
    public function end($data = null, $encoding = null)
    {
        $this->send($data);
        
        if($encoding){
            $this->set('Content-Type', 'text/html;charset=' . $encoding);
        }
        
        if(!isset($this->headers['Content-Length'])){
            $this->set('Content-Length', strlen($this->body));
        }
        
        header(sprintf(
            'HTTP/%s %d%s',
            $this->protocol,
            $this->status,
            $this->phrase()
        ));
        
        foreach ($this->headers as $name => $values) {
            $name = $this->filter($name);
            $first = true;
            foreach ($values as $value) {
                header(sprintf(
                    '%s: %s',
                    $name,
                    $value
                    ), $first);
                $first = false;
            }
        }
        
        echo $this->body;
    }
    
    protected function phrase()
    {
        return isset($this->phrases[$this->status])?$this->phrases[$this->status]:'';
    }
    
    protected function filter($name)
    {
        return str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::format()
     */
    public function format($object)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::get()
     */
    public function get($field)
    {
        // TODO Auto-generated method stub
        return $this->headers[$field];
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::json()
     */
    public function json($body)
    {
        $this->send(json_encode($body));
        
        return $this;
    }

    public function jsonp($body)
    {
        $this->send($this->app->_get('jsonp callback name') . '('.json_encode($body).')');
        
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::links()
     */
    public function links($links)
    {
        // TODO Auto-generated method stub


        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::location()
     */
    public function location($path)
    {
        // TODO Auto-generated method stub
        $this->append('Location', $path);
        
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::redirect()
     */
    public function redirect($path, $status = null)
    {
        // TODO Auto-generated method stub
        $this->status($status);
        
        $this->location($path);
        
        return $this;
    }

    public function render($view, $locals = [], $callback = null)
    {
        $this->send($this->app->render($view, $locals, $callback));
        
        return $this;
    }

    public function send($body)
    {
        $this->body .= $body;
        
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::sendFile()
     */
    public function sendFile($path, $options = null, $fn = null)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::sendStatus()
     */
    public function sendStatus($statusCode)
    {
        // TODO Auto-generated method stub
        $this->status = $statusCode;
        
        return $this;
    }

    public function set($field, $value)
    {
        $this->headers[$field] = array($value);
        
        return $this;
    }
    
    public function status($code)
    {
        $this->status = $code;
        
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::type()
     */
    public function type($type)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \App\Interfaces\ResponseInterface::vary()
     */
    public function vary($field)
    {
        // TODO Auto-generated method stub
        
    }

}
