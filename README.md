# php-express
A pipeline framework use express.js api and reactphp framework for php

# example 

```php
$app = Express::Application();

$app->get('/', function(Request $req, Response $res){
    return $res->send('Hello World');
});

$app->listen(8080);
```

# example 2 sub router

```php
$app = Express::Application();

$post = Express::Router();

$post->get('/:id', function(Request $req, Response $res){
    return $res->send('Hello Post:' . $req->params['id']);
});

$post->get('/', function(Request $req, Response $res){
    return $res->send('Hello Post');
});

$app->use('/post', $post);

$app->get('/', function(Request $req, Response $res){
    return $res->send('Hello World');
});

$app->listen(8080);
```

# example 3 middleware use

```php
$app = Express::Application();

$app->use(Express::static(__DIR__));

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
```
# todo
some method need complete

# log

