<?php

use Logcomex\PhpUtils\Singletons\TracerSingleton;

/**
 * Class TracerSingletonUnitTest
 */
class TracerSingletonUnitTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        TracerSingleton::setTraceValue('');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        TracerSingleton::setTraceValue('');
    }

    /**
     * @return void
     */
    public function testGetTraceValue_SuccessFlow(): void
    {
        TracerSingleton::setTraceValue('test');

        $response = TracerSingleton::getTraceValue();
        $this->assertIsString($response);
        $this->assertEquals('test', $response);
    }

    /**
     * @return void
     */
    public function testSetTraceValue_SuccessFlow(): void
    {
        $response = TracerSingleton::setTraceValue('test');

        $this->assertNull($response);

        $this->assertIsString(TracerSingleton::getTraceValue());
        $this->assertEquals('test', TracerSingleton::getTraceValue());
    }
}
