<?php

/**
 * Class LoggerUnitTest
 * @package Tests\Unit\Facades
 */
class LoggerUnitTest extends TestCase
{
    /**
     * @void
     */
    public function testGetFacadeAccessor(): void
    {
        $response = LoggerFacadePublic::getFacadeAccessor();

        $this->assertIsString($response);
        $this->assertEquals('Logger', $response);
    }
}
