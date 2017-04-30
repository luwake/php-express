<?php
require __DIR__.'/../vendor/autoload.php';

$app = new \Luwake\Express();
$app->get('/', function(\Luwake\Request $request, \Luwake\Response $response, $next){
	return $response->send('Hello World');
});
$app->listen();