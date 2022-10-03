<?php

namespace Tests\Helpers;

use Logcomex\PhpUtils\Exceptions\ApiException;
use Logcomex\PhpUtils\Helpers\ExceptionHelper;
use Tests\TestCase;

/**
 * Class ExceptionHelperUnitTest
 * @package Tests\Unit\Helpers
 */
class ExceptionHelperUnitTest extends TestCase
{
    /**
     * @var ExceptionHelper
     */
    private $helper;

    public function setUp(): void
    {
        parent::setUp();
        $this->helper = new ExceptionHelper();
    }

    /**
     * @return void
     */
    public function testExportExceptionToArrayWithArrayableException(): void
    {
        $exception = new ApiException('T01', 'Teste', 404);

        $response = $this->helper::exportExceptionToArray($exception);

        $this->assertIsArray($response);
    }

    /**
     * @return void
     */
    public function testExportExceptionToArrayWithDefaultException(): void
    {
        $exception = new \Exception('Teste', 100);

        $exceptionArray = $this->helper::exportExceptionToArray($exception);
        $this->assertIsArray($exceptionArray);
        $this->assertEquals('Exception', $exceptionArray['exception-class']);
        $this->assertEquals('Teste', $exceptionArray['message']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }
}
