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

        $logFilePath = storage_path('logs/lumen.log');
        if (file_exists($logFilePath)) {
            unlink($logFilePath);
        }
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

    /**
     * @param object $object
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     * @throws ReflectionException
     */
    public function invokeNonPublicMethod(object $object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return mixed
     * @throws ReflectionException
     */
    public function getNonPublicProperty(object $object, string $propertyName)
    {
        $allChainClasses = [];
        $value = null;

        $reflection = new ReflectionClass(get_class($object));
        $allChainClasses[] = $reflection;

        $parentClass = $reflection->getParentClass();
        while ($parentClass != false) {
            $allChainClasses[] = $parentClass;
            $parentClass = $parentClass->getParentClass();
        }

        foreach ($allChainClasses as $reflectionClass) {
            if (isset($value)) {
                return $value;
            }

            $classDoesNotHaveProperty = !$reflectionClass->hasProperty($propertyName);
            if ($classDoesNotHaveProperty) {
                continue;
            }

            $requestedProperty = $reflectionClass->getProperty($propertyName);
            $requestedProperty->setAccessible(true);
            $value = $requestedProperty->getValue($object);
        }

        return $value;
    }

    /**
     * @param string $expectedLogContent
     * @return void
     */
    protected function assertLogContent(string $expectedLogContent): void
    {
        $logContent = file_get_contents(storage_path('logs/lumen.log'));
        $this->assertStringContainsString($expectedLogContent, $logContent);
    }

    protected function getLastLogJson(): array
    {
        $logContent = file_get_contents(storage_path('logs/lumen.log'));
        $explodedLogContent = explode(')', $logContent);

        return json_decode($explodedLogContent[1], true);
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
