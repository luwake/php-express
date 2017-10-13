<?php
namespace Luwake;

use Opis\Http\Response as HttpResponse;
use Luwake\Traits\ObjectTrait;
use Luwake\Traits\ExtendTrait;

class Response extends HttpResponse implements \ArrayAccess
{
    use ObjectTrait,ExtendTrait;

    public $app;

    public function __construct(Express $app)
    {
        parent::__construct($app->request());
        $this->app = $app;
    }

    public function download($path, $filename = null, $fn = null)
    {}

    public function end($data = null, $encoding = null)
    {}

    public function format($object)
    {}

    public function get($field)
    {
        return isset($this->headers[$field]) ? $this->headers[$field] : false;
    }

    public function set($field, $value = null)
    {
        if (is_array($field)) {
            return $this->headers($field);
        }
        return $this->header($field, $value);
    }

    public function json($body = null)
    {
        return $this->write(json_encode($body))->contentType('application/json');
    }

    public function jsonp($body = null)
    {
        $callback = $this->app->get('jsonp callback name');
        return $this->write($callback . '(' . json_encode($body) . ')')->contentType('application/json');
    }

    public function links($links)
    {}

    public function location($path)
    {
        $this->set('Location', $path);
        return $this;
    }

    public function render($view, $locals = [], $handle = null)
    {
        return $this->app->render($view, $locals, $handle);
    }

    public function write($body = null)
    {
        return $this->body($body);
    }

    public function sendFile($path, $options = null, $fn = null)
    {}

    public function sendStatus($statusCode)
    {
        return $this->status($statusCode);
    }

    public function vary($field)
    {}

    public function append($field, $value = null)
    {
        return $this->set($field, $value);
    }

    public function attachment($filename = null)
    {}

    public function clearCookie($name, $options = [])
    {
        $this->deleteCookie($name, $options);
    }
}