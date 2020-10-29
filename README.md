# php-utils

PHP Utilities for Laravel/Lumen

## Installation 

```sh
cd /path/to/your/project
composer require logcomex/php-utils
```

## Utilities Packages

 - Contracts
 - Exceptions
 - Functionalities
 - Helpers
 - Handlers
 - Logs
 - Middlewares
 - Singletons

## Contracts

> Have all the contracts (interfaces) used by the php-utils classes and other that you can use in your project.
  
## Exceptions  

> Have all the exceptions used by the php-utils classes. 
> And others that you can use in your project to segregate your errors types .

#### ApiException

> You can use for all exceptions in 400 range http code

``` php
ApiException(string $token,
			string $message, 
			int $httpCode = Response::HTTP_BAD_REQUEST, 
			Exception $previous = null)  
```
| Visibility | Function		  | Return Type |
| ---------- | -------------- | ----------- |
| public 	 | getHttpCode    | int 		|
| public 	 | getToken		  | string 		|
| public 	 | __toString     | string 		|
| public 	 | toArray 		  | array 		|
| public 	 | toJson 		  | string 		|

#### BadImplementationException

> This exception means that a situation has been overlooked or incorrectly done by the developer.

``` php
BadImplementationException(string $token,
            string $message,
			int $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR, 
			Exception $previous = null)  
```
| Visibility | Function		  | Return Type |
| ---------- | -------------- | ----------- |
| public 	 | getHttpCode    | int 		|
| public 	 | getToken		  | string 		|
| public 	 | __toString     | string 		|
| public 	 | toArray 		  | array 		|
| public 	 | toJson 		  | string 		|

#### SecurityException

> This exception serves to point out some security problem in your application.

``` php
SecurityException(string $token,
			string $message, 
			int $httpCode = Response::HTTP_FORBIDDEN, 
			Exception $previous = null)  
```
| Visibility | Function		  | Return Type |
| ---------- | -------------- | ----------- |
| public 	 | getHttpCode    | int 		|
| public 	 | getToken		  | string 		|
| public 	 | __toString     | string 		|
| public 	 | toArray 		  | array 		|
| public 	 | toJson 		  | string 		|

#### UnavailableServiceException

> This exception serves to point out that your or other application is unavailable.

``` php
UnavailableServiceException(string $token,
			string $message, 
			int $httpCode = Response::HTTP_FORBIDDEN, 
			Exception $previous = null)  
```
| Visibility | Function		  | Return Type |
| ---------- | -------------- | ----------- |
| public 	 | getHttpCode    | int 		|
| public 	 | getToken		  | string 		|
| public 	 | getService	  | string 		|
| public 	 | __toString     | string 		|
| public 	 | toArray 		  | array 		|
| public 	 | toJson 		  | string 		|

## Functionalities  

> They're a pack of traits that can be useful in your code  

#### PropertiesExporterFunctionality

> You can use this functionality to export an array with you class properties

``` php
public static function properties(): array  
```

#### PropertiesAttacherFunctionality

> You can use this functionality to attach in your class properties the values passed in the parameter.
> >   **Note:** To uses this functionality, you need use the PropertiesExporterFunctionality in the class.

``` php
public function attachValues(array $values): void
```
| Exception  | Reason |
| ---------- | ----------- |
| BadImplementationException | When you don't use PropertiesExporterFunctionality |

#### ValuesExporterToArrayFunctionality

> You can use this functionality to easily get all the properties of class in an array.
> >   **Note:** To uses this functionality, you need to do two things:
> >  1. The class must implement Illuminate\Contracts\Support\Arrayable.
> >  2. The class must use PropertiesExporterFunctionality.

``` php
public function toArray()  
```
| Exception  | Reason |
| ---------- | ----------- |
| BadImplementationException | When yout don't implement the the Arrayable contract |
| BadImplementationException | When you don't use PropertiesExporterFunctionality |

#### ValuesExporterToJsonFunctionality

> You can use this functionality to easily get all the properties of class in a Json.
> >   **Note:** To uses this functionality, you need to do two things:
> >  1. The class must implement Illuminate\Contracts\Support\Jsonable.
> >  2. The class must use PropertiesExporterFunctionality.

``` php
public function toJson()  
```
| Exception  | Reason |
| ---------- | ----------- |
| BadImplementationException | When yout don't implement the the Jsonable contract |
| BadImplementationException | When you don't use PropertiesExporterFunctionality |

## Helpers  

> They're a pack of Helpers classes and traits.

#### EnumHelper

> It's a trait that provide some utilities to your Enumerators classes.

| Visibility | Function | Return Type | Purpose |
| :--- | :--- | :--- |:--- |
| public 	 | all | array | Get all the constants of your Enumerator |

``` php
use Logcomex\PhpUtils\Helpers\EnumHelper;

class ProductEnum
{
	user EnumHelper;
	public const EXAMPLE = 'example';
	public const EXAMPLE2 = 'example2';
}

$allProducts = ProductEnum::all();
```

## Middlewares  

> They're a pack of Middleware classes.

#### AuthenticateMiddleware

> It is a class that provides authentication verification. 
> You'll need a AuthProvider configured in your application to use this Middleware.

``` php
// bootstrap/app.php
$app->register(Your\Provider\AuthServiceProvider::class);

// Using in global mode
$app->middleware([
    Logcomex\PhpUtils\Middleware\AuthenticateMiddleware::class,
]);

// Or, by specific route
$app->routeMiddleware([
    'auth' => Logcomex\PhpUtils\Middleware\AuthenticateMiddleware::class,
]);
```

#### RequestLogMiddleware

> It is a class that provides a log for each request in your api.
> You can choose what you gonna print in the log, such as: request-header, request-server, request-payload,
> response-header, response-content, response-time, and trace-id.
> <br><br> The .env configuration:

| Env Variable | Type		  | Description |
| ---------- | -------------- | ----------- |
| REQUEST_LOGGER_ENABLE_REQUEST_HEADER | boolean | Print in the log, the request header information |
| REQUEST_LOGGER_ENABLE_REQUEST_SERVER | boolean | Print in the log, the request server information |
| REQUEST_LOGGER_ENABLE_REQUEST_PAYLOAD | boolean | Print in the log, the request payload information |
| REQUEST_LOGGER_ENABLE_RESPONSE_HEADER | boolean | Print in the log, the response header information |
| REQUEST_LOGGER_ENABLE_RESPONSE_CONTENT | boolean | Print in the log, the response content information |
| REQUEST_LOGGER_ENABLE_RESPONSE_TIME | boolean | Print in the log, the response execution time information |
| REQUEST_LOGGER_ALLOWED_DATA_REQUEST_SERVER | string | If has data in this variable, the middleware gonna print just the infos requested in this setting |

``` php
// config/requestLog.php
return [
    'enable-request-header' => env('REQUEST_LOGGER_ENABLE_REQUEST_HEADER', true),
    'enable-request-server' => env('REQUEST_LOGGER_ENABLE_REQUEST_SERVER', true),
    'enable-request-payload' => env('REQUEST_LOGGER_ENABLE_REQUEST_PAYLOAD', true),
    'enable-response-header' => env('REQUEST_LOGGER_ENABLE_RESPONSE_HEADER', true),
    'enable-response-content' => env('REQUEST_LOGGER_ENABLE_RESPONSE_CONTENT', true),
    'enable-response-time' => env('REQUEST_LOGGER_ENABLE_RESPONSE_TIME', true),
    'allowed-data-request-server' => explode(';', env('REQUEST_LOGGER_ALLOWED_DATA_REQUEST_SERVER', '')),
];


// bootstrap/app.php
$app->configure('requestLog');

// Using in global mode
$app->middleware([
    Logcomex\PhpUtils\Middleware\TracerMiddleware::class, // If you gonna use tracer, it must be above the requestlog
    Logcomex\PhpUtils\Middleware\RequestLogMiddleware::class, // And after trace, you need the request log
]);
```

#### ResponseTimeLogMiddleware

> It is a class that registers the response time of each request in your api.
> You can choose what request will be measured through calling the middleware by route.

First of all, you have to define the framework start time globally before requiring anything in your bootstrap:

``` php
// bootstrap/app.php
if (!defined('GLOBAL_FRAMEWORK_START')) {
    define('GLOBAL_FRAMEWORK_START', microtime(true));
}
```

Configuration:

``` php
// config/responseTimeLog.php
return [
    'api-name' => '',
];

// bootstrap/app.php
$app->configure('responseTimeLog');
```

Usage:

``` php
// Using in global mode
$app->middleware([
    Logcomex\PhpUtils\Middleware\ResponseTimeLogMiddleware::class, // And after trace, you need the request log
]);

// Using in specific routes
$app->routeMiddleware([
    'response-time-log' => Logcomex\PhpUtils\Middlewares\ResponseTimeLogMiddleware::class,
]);
Route::group(
    [
        'prefix' => 'example',
        'middleware' => ['response-time-log'],
    ],
    function () {
        Route::get('responseTimeLog', 'ExampleClassName@exampleMethodName');
    });
```

#### TracerMiddleware

> It is a class that provides tracer functionality for your api.
> So your log can use this value and the HttpHelper.
> You must create a tracer config file in config folder. 
> We recommend uses this middleware as global, and the first one in middlewares chain.

``` php
// config/tracer.php
return [
    'headersToPropagate' => explode(';', env('TRACER_HEADERS_TO_PROPAGATE')),
];


// bootstrap/app.php
$app->configure('tracer');

// Using in global mode
$app->middleware([
    Logcomex\PhpUtils\Middleware\TracerMiddleware::class,
    // the other middlewares
]);

// Or, by specific route
$app->routeMiddleware([
    'tracer' => Logcomex\PhpUtils\Middleware\TracerMiddleware::class,
]);
```

#### AccreditedApiKeysMiddleware

> It is a class that provides a first level of security for your api. The best analogy is that middleware
> is your "API guest list". <br> <br>
> You need to register a configuration file called accreditedApiKeys, with all 
> the api-keys that can request your api. <br> <br>
> Therefore, if the request does not contain the x-infra-key header or a allowed value, the API denies the request 
> with the security exception. <br> <br>
> It is recommended to use as a global middleware, and if you need to avoid this middleware for some routes, just 
> insert into the public route group.

``` php
// config/accreditedApiKeys.php
return [
    'api-1' => env('API_1_X_API_KEY'),
    'api-2' => env('API_2_X_API_KEY'),
    'api-3' => env('API_3_X_API_KEY'),
];


// bootstrap/app.php
$app->configure('accreditedApiKeys');

// Using in global mode
$app->middleware([
    Logcomex\PhpUtils\Middleware\AccreditedApiKeysMiddleware::class,
]);


// routes/api.php
$router->group(['prefix' => 'public',], function () use ($router) {
    $router->get('test', 'Controller@test');// this route does not need x-infra-key validation
});
```

## Singletons  

> They're a pack of Singleton classes.

#### TracerSingleton

> It is a class that provides the tracer value.

## Unit Tests Coverage

Master <br>
[![codecov](https://codecov.io/gh/comexio/php-utils/branch/master/graph/badge.svg)](https://codecov.io/gh/comexio/php-utils)

## TODO

 - [ ] HttpHelper Doc
 - [ ] TokenHelper Doc
 - [ ] Handlers Package Doc
	 - [ ] ExceptionHandler Doc
 - [ ] Logs Package Doc
	 - [ ] RequestLog Doc
 - [ ] Middlewares Package Doc
	 - [ ] AllowedHostsMiddleware Doc
	 - [ ] CorsMiddleware Doc

## Contributing  
  
- Open an issue first to discuss potential changes/additions.
- Open a pull request, you need two approvals and tests need to pass Travis CI.
