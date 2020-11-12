<?php

namespace Logcomex\PhpUtils\Dto;

use Logcomex\PhpUtils\Functionalities\PropertiesAttacherFunctionality;
use Logcomex\PhpUtils\Functionalities\PropertiesExporterFunctionality;

/**
 * Class ResponseTimePayloadDto
 * @package Logcomex\PhpUtils\Dto
 */
class ResponseTimePayloadDto extends Dto
{
    use PropertiesExporterFunctionality,
        PropertiesAttacherFunctionality;
    /**
     * @var string
     */
    public $api = '';
    /**
     * @var string
     */
    public $endpoint = '';
    /**
     * @var float
     */
    public $response_time = 0.0;
    /**
     * @var object | array
     */
    public $payload = '';
    /**
     * @var string
     */
    public $created_at = '';
}
