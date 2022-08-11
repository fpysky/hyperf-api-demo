<?php

declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/aaa', function () {
    return ['message' => 'hello world!'];
});

Router::addRoute(['GET', 'POST', 'HEAD'], '/test', \App\Controller\TestController::class . '@test');

Router::get('/favicon.ico', function () {
    return '';
});
