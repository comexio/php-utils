<?php

namespace Tests\Providers;

use Logcomex\PhpUtils\Loggers\LogcomexLogger;
use Logcomex\PhpUtils\Providers\LogcomexLoggerProvider;
use Tests\TestCase;

/**
 * Class LogcomexLoggerProviderUnitTest
 */
class LogcomexLoggerProviderUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testRegisterSuccessFlow(): void
    {
        $provider = new LogcomexLoggerProvider(app());
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = $provider->register();

        $this->assertNull($response);
        $this->assertInstanceOf(LogcomexLogger::class, app()['Logger']);
    }
}
