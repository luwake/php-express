<?php
use Luwake\Express;
use Luwake\Request;
use Luwake\Response;

require __DIR__.'/../vendor/autoload.php';

$app = new Express('/express');

$app->use(function(Request $req, Response $res, $next){
    return $next($req, $res);
});

$admin = $app->route('/admin');

$admin->use(function(Request $req, Response $res, $next){
    return $next($req, $res);
});

$admin->get('/', function(Request $req, Response $res, $next)use($admin){
    return $res->jsonp(array('status'=>1,'msg'=>'操作失败'));
});

$app->get('/', function(Request $req, Response $res, $next){
    return $res->write('<p>Hello World</p>');
});

$app->listen();