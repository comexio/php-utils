<?php

namespace Logcomex\PhpUtils\Functionalities;

use Illuminate\Contracts\Support\Arrayable;
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
                'You must implement the the Arrayable contract to use this functionality'
            );
        }

        if (!method_exists($this, 'properties')) {
            throw new BadImplementationException(
                'You must use the Trait PropertiesExporterFunctionality to use this functionality'
            );
        }
        $response = [];

        foreach ($this->properties() as $propertyClass) {
            $response[$propertyClass] = $this->{$propertyClass};
        }

        return $response;
    }
}
