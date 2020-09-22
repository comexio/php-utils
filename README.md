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

#### TracerMiddleware

> It is a class that provides tracer functionality for your api.
> So your log can use this value and the HttpHelper.
> You must create a tracer config file in config folder. 
> We recommend uses this middleware as global, and the first one in middlewares chain.

``` php
// config/tracer.php
return [
    'headersToPropagate' => explode(',', env('TRACER_HEADERS_TO_PROPAGATE')),
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

## Singletons  

> They're a pack of Singleton classes.

#### TracerMiddleware

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
	 - [ ] AccreditedApiKeysMiddleware Doc
	 - [ ] AllowedHostsMiddleware Doc
	 - [ ] CorsMiddleware Doc
	 - [ ] RequestLogMiddleware Doc

## Contributing  
  
- Open an issue first to discuss potential changes/additions.
- Open a pull request, you need two approvals and tests need to pass Travis CI.
