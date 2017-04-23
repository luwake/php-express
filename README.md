# php-express
A pipeline framework use express.js api for php

# example

```php
$app = new Express();

$app->set('views', __DIR__ . '/views');

$app->use('App\Middlewares\MethodOverride');

$app->use('/admin', require __DIR__ . '/routes/admin.php');

$app->use('/', require __DIR__ . '/routes/home.php');

$app->listen();
```
