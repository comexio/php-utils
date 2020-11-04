<?php

namespace Logcomex\PhpUtils\Functionalities;

use Illuminate\Contracts\Support\Jsonable;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;

/**
 * Trait ValuesExporterToJsonFunctionality
 * @package Logcomex\PhpUtils\Functionalities
 */
trait ValuesExporterToJsonFunctionality
{
    /**
     * @param int $options
     * @return string
     * @throws BadImplementationException
     */
    public function toJson($options = 0)
    {
        if (!($this instanceof Jsonable)) {
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
            $response[$propertyClass] = $this->{$propertyClass};
        }

        return json_encode($response, $options);
    }
}
