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
    public function testGetTraceValue_SuccessFlow(): void
    {
        TracerSingleton::setTraceValue('test');
        try {
            $response = TracerSingleton::getTraceValue();
            $this->assertIsString($response);
            $this->assertEquals('test', $response);
        } finally {
            TracerSingleton::setTraceValue('');
        }
    }

    /**
     * @return void
     */
    public function testSetTraceValue_SuccessFlow(): void
    {
        TracerSingleton::setTraceValue('test');
        try {
            $response = TracerSingleton::setTraceValue('test');

            $this->assertNull($response);

            $this->assertIsString(TracerSingleton::getTraceValue());
            $this->assertEquals('test', TracerSingleton::getTraceValue());
        } finally {
            TracerSingleton::setTraceValue('');
        }
    }
}
