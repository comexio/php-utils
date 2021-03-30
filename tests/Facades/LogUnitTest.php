<?php

/**
 * Class LogUnitTest
 * @package Tests\Unit\Facades
 */
class LogUnitTest extends TestCase
{
    /**
     * @void
     */
    public function testGetFacadeAccessor(): void
    {
        $response = LogFacadePublic::getFacadeAccessor();

        $this->assertIsString($response);
        $this->assertEquals('Log', $response);
    }
}
