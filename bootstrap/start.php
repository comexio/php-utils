<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->register(Logcomex\PhpUtils\Providers\LogcomexLoggerProvider::class);

$app->withFacades(true, [
    Logcomex\PhpUtils\Facades\Logger::class => 'Logger',
]);

$app->configure('app');
$app->configure('cors');
$app->configure('logging');

$app->routeMiddleware([
    'request-log' => Logcomex\PhpUtils\Middlewares\RequestLogMiddleware::class,
    'response-time-log' => Logcomex\PhpUtils\Middlewares\ResponseTimeLogMiddleware::class,
    'allowed-hosts' => Logcomex\PhpUtils\Middlewares\AllowedHostsMiddleware::class,
    'cors' => Logcomex\PhpUtils\Middlewares\CorsMiddleware::class,
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
    $router->group(['middleware' => ['response-time-log']], function () use ($router, $routeFunction) {
        $router->get('response-time-log-middleware', function() {
            sleep(2);
            return response()->json(["Request should spend more than 2 seconds."]);
        });
    });
    $router->group(['middleware' => ['allowed-hosts']], function () use ($router, $routeFunction) {
        $router->get('allowed-hosts-middleware', $routeFunction);
    });
    $router->group(['middleware' => ['trace']], function () use ($router, $routeFunction) {
        $router->get('trace', $routeFunction);
    });
});

return $app;

