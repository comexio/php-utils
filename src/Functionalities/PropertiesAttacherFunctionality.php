<?php

namespace Logcomex\PhpUtils\Functionalities;

use Logcomex\PhpUtils\Exceptions\BadImplementationException;

/**
 * Trait PropertiesAttacherFunctionality
 * @package Logcomex\PhpUtils\Functionalities
 */
trait PropertiesAttacherFunctionality
{
    /**
     * @param array $values
     * @throws BadImplementationException
     */
    public function attachValues(array $values): void
    {
        if (!method_exists($this, 'properties')) {
            throw new BadImplementationException(
                'You must use the Trait PropertiesExporterFunctionality to use this functionality'
            );
        }

        $properties = $this->properties();
        foreach ($values as $propertyName => $propertyValue) {
            if (!in_array($propertyName, $properties)) {
                continue;
            }

            $this->{$propertyName} = $propertyValue;
        }
    }
}
