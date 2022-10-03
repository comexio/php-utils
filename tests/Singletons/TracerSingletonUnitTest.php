<?php

namespace Tests\Singletons;

use Logcomex\PhpUtils\Singletons\TracerSingleton;
use Tests\TestCase;

/**
 * Class TracerSingletonUnitTest
 */
class TracerSingletonUnitTest extends TestCase
{
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
    public function testGetTraceValue_HappyPath_SuccessFlow(): void
    {
        TracerSingleton::setTraceValue('test');

        $response = TracerSingleton::getTraceValue();
        $this->assertIsString($response);
        $this->assertEquals('test', $response);
    }

    /**
     * @return void
     */
    public function testGetTraceValue_EmptyValue_SuccessFlow(): void
    {
        $response = TracerSingleton::getTraceValue();
        $this->assertIsString($response);
        $this->assertEquals('TRACE_NOT_IMPLEMENTED', $response);
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
