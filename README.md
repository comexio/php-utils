# php-utils
PHP Utilities for Laravel/Lumen

## Installation

    composer require logcomex/php-utils
    
## Utilities Documentation

#### Exceptions

You can use the exceptions to segregate your errors types

* ##### ApiException(`string $token`, `string $message`, `int $httpCode = Response::HTTP_BAD_REQUEST`, `Exception $previous = null`)
    
    > ##### public function getHttpCode(): int

    > ##### public function getToken(): string

    > ##### public function __toString(): string

    > ##### public function toArray(): array

    > ##### public function toJson(): string

* ##### BadImplementationException(`string $message`, `int $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR`, `Exception $previous = null`)
    
    > ##### public function getHttpCode(): int

    > ##### public function __toString(): string

    > ##### public function toArray(): array

    > ##### public function toJson(): string

* ##### SecurityException(`string $token`, `string $message`, `int $httpCode = Response::HTTP_FORBIDDEN`, `Exception $previous = null`)
    
    > ##### public function getHttpCode(): int

    > ##### public function getToken(): string

    > ##### public function __toString(): string

    > ##### public function toArray(): array

    > ##### public function toJson(): string

#### Functionalities

They're a pack of traits that can be useful in your code

* ##### PropertiesExporterFunctionality
    `You can use this functionality to export an array with you class properties`
    
    > ##### public static function properties(): array

* ##### PropertiesAttacherFunctionality
    `You can use this functionality to attach in your class properties the values passed in the parameter.`
    
    **Note:** `To uses this functionality, you need use the PropertiesExporter functionality in the class.`

    > ##### public function attachValues(array $values): void

## Contributing

Open an issue first to discuss potential changes/additions.
