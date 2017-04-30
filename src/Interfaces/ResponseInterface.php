<?php
namespace Luwake\Interfaces;

interface ResponseInterface
{

    public function append($field, $value = null);

    public function attachment($filename = null);

    public function cookie($name, $value, $options = null);

    public function clearCookie($name, $options = null);

    public function download($path, $filename = null, $fn = null);

    public function end($data = null, $encoding = null);

    public function format($object);

    public function get($field);

    public function set($field, $value = null);

    public function json($body = null);

    public function jsonp($body = null);

    public function links($links);

    public function location($path);

    public function redirect($status = null, $path);

    public function render($view, $locals = null, $callback = null);

    public function send($body = null);

    public function sendFile($path, $options = null, $fn = null);

    public function sendStatus($statusCode);

    public function status($code);

    public function type($type);

    public function vary($field);
}