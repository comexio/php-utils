<?php

use Illuminate\Support\Facades\File;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Logcomex\PhpUtils\Contracts\MockContract;

/**
 * Class TestCase
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/start.php';
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'app.mode' => 'test',
            'mockedEndpoints' => [
                'api/mocked' => FakeMock::class,
            ]
        ]);
    }

    /**
     * @param Exception $exception
     * @param string $expectedToken
     */
    public function expectExceptionToken(Exception $exception, string $expectedToken)
    {
        $this->assertEquals($expectedToken, $exception->getToken(), 'Exception token is not expected.');
    }

    /**
     * @param Exception $expectedException
     * @param Closure $handler
     * @throws Exception
     */
    public function expectCustomException(Exception $expectedException, Closure $handler): void
    {
        try {
            $this->expectExceptionObject($expectedException);
            $handler();
        } catch (Exception $exception) {
            if (method_exists($exception, 'getToken')) {
                $this->expectExceptionToken($exception, $expectedException->getToken());
            }

            throw $exception;
        }
    }

    /**
     * @param string $filePath
     * @return string
     */
    public function getJsonFile(string $filePath): string
    {
        return trim(json_encode(json_decode(File::get($filePath))));
    }
}

/**
 * Class FakeMock
 */
class FakeMock implements MockContract
{
    /**
     * @return string
     */
    public static function mock(): string
    {
        return '';
    }
}
