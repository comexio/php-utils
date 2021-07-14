<?php

namespace Logcomex\PhpUtils\Functionalities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;

/**
 * Trait ValuesExporterToJsonFunctionality
 * @package Logcomex\PhpUtils\Functionalities
 */
trait ValuesExporterToSnakeCaseToArrayFunctionality
{
    /**
     * @return array
     * @throws BadImplementationException
     */
    public function toSnakeCase(): array
    {
        if (!($this instanceof Arrayable)) {
            throw new BadImplementationException(
                ErrorEnum::PHU006,
                'You must implement the Jsonable contract to use this functionality.'
            );
        }

        if (!method_exists($this, 'properties')) {
            throw new BadImplementationException(
                ErrorEnum::PHU001,
                'You must use the Trait PropertiesExporterFunctionality to use this functionality.'
            );
        }
        $response = [];

        foreach ($this->properties() as $propertyClass) {
            $response[Str::snake($propertyClass)] = $this->{$propertyClass};
        }

        return $response;
    }
}
