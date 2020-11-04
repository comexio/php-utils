<?php

namespace Logcomex\PhpUtils\Functionalities;

use Illuminate\Contracts\Support\Arrayable;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;

/**
 * Trait ValuesExporterToArrayFunctionality
 * @package Logcomex\PhpUtils\Functionalities
 */
trait ValuesExporterToArrayFunctionality
{
    /**
     * @return array
     * @throws BadImplementationException
     */
    public function toArray()
    {
        if (!($this instanceof Arrayable)) {
            throw new BadImplementationException(
                ErrorEnum::PHU002,
                'You must implement the Arrayable contract to use this functionality.'
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

        return $response;
    }
}
