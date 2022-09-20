<?php

namespace Tests\Loggers;

use Illuminate\Contracts\Support\Arrayable;
use Logcomex\PhpUtils\Dto\Dto;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;
use Logcomex\PhpUtils\Functionalities\ValuesExporterToArrayFunctionality;

/**
 * Class FakeDtoWithArrayable
 * @package Tests\Unit\Loggers
 */
class FakeDtoWithArrayable extends Dto implements Arrayable
{
    use PropertiesExporterFunctionality,
        ValuesExporterToArrayFunctionality;

    /**
     * @var string
     */
    public $test;
}
