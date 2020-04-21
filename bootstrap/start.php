<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->configure('app');
$app->configure('cors');

$app->routeMiddleware([
    'request-log' => \Logcomex\PhpUtils\Middlewares\RequestLogMiddleware::class,
    'cors' => \Logcomex\PhpUtils\Middlewares\CorsMiddleware::class,
]);

$app->router->group([], function ($router) {
    $routeFunction = function () {
        return 'OK';
    };

    $router->group(['middleware' => ['cors']], function () use ($router, $routeFunction) {
        $router->get('cors-middleware', $routeFunction);
        $router->options('cors-middleware', $routeFunction);
        $router->post('cors-middleware', $routeFunction);
        $router->patch('cors-middleware', $routeFunction);
        $router->delete('cors-middleware', $routeFunction);
    });
    $router->group(['middleware' => ['request-log']], function () use ($router, $routeFunction) {
        $router->get('request-log-middleware', $routeFunction);
    });
});

$app->withFacades();

return $app;

