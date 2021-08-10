<?php
use Luwake\Express;
use Luwake\Request;
use Luwake\Response;

require __DIR__.'/../vendor/autoload.php';

$app = Express::Application();

$app->use(Express::static(__DIR__));

$post = Express::Router();

$post->get('/:id', function(Request $req, Response $res){
    return $res->send('Hello Post:' . $req->params['id']);
});

$post->get('/', function(Request $req, Response $res){
    return $res->send('Hello Post');
});

$app->use('/post', $post);

$api = Express::Router('/api');

$api->use(Express::json());

$api->get('/', function(Request $req, Response $res){
    return [
        'code' => 0,
        'msg' => '',
        'data' => [],
    ];
});

$app->use($api);

$app->get('/', function(Request $req, Response $res){
    return $res->send('Hello World');
});

$app->listen(8080);
