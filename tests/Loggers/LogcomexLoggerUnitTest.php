<?php

namespace Tests\Loggers;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Logcomex\PhpUtils\Facades\Logger;
use Logcomex\PhpUtils\Loggers\LogcomexLogger;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Tests\TestCase;

/**
 * Class LogcomexLoggerUnitTest
 * @package Tests\Unit\Loggers
 */
class LogcomexLoggerUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testTreatContextEmptyPayloadSuccessFlow(): void
    {
        $response = LogcomexLogger::treatContext([]);

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    /**
     * @return void
     */
    public function testTreatContextArrayNotEmptySuccessFlow(): void
    {
        $expectedResponse = ['test' => 'ok',];
        $response = LogcomexLogger::treatContext($expectedResponse);

        $this->assertIsArray($response);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     */
    public function testTreatContextObjectSuccessFlow(): void
    {
        $expectedResponse = ['test' => 'ok',];
        $fakeObjectFormat = json_decode(json_encode($expectedResponse));
        $response = LogcomexLogger::treatContext($fakeObjectFormat);

        $this->assertIsArray($response);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testTreatContextDtoWithArrayableSuccessFlow(): void
    {
        $fakeDto = new FakeDtoWithArrayable();
        $response = LogcomexLogger::treatContext($fakeDto);

        $this->assertIsArray($response);
        $this->assertEquals($fakeDto->toArray(), $response);
    }

    /**
     * @return void
     */
    public function testTreatContextDtoWithoutArrayableSuccessFlow(): void
    {
        $fakeDto = new FakeDtoWithoutArrayable();
        $response = LogcomexLogger::treatContext($fakeDto);

        $this->assertIsArray($response);
        $this->assertEquals((array)$fakeDto, $response);
    }

    /**
     * @return void
     */
    public function testTreatContextExceptionNullSuccessFlow(): void
    {
        $response = LogcomexLogger::treatContext();

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    /**
     * @return void
     */
    public function testTreatContextExceptionNotNullSuccessFlow(): void
    {
        $exception = new BadImplementationException('test', 'testMessage');
        $fakeDto = new FakeDtoWithoutArrayable();
        $response = LogcomexLogger::treatContext($fakeDto, $exception);

        $this->assertIsArray($response);
        $this->assertEquals([
            'test',
            'exception-class',
            'message',
            'file',
            'line',
            'http-code'
        ], array_keys($response));
    }

    /**
     * @return void
     */
    public function testTreatContextGuzzleExceptionWithStringLineBreaks(): void
    {
        $exception = new BadResponseException(
            "Client Error:'POST http://localhost response:[{error: true, message:'Tracking\nalready\nexists\n'}]",
            new Request('POST', 'http://localhost'),
            new Response()
        );
        $context = [
            'fieldWithDoubleQuotes' => "Test\n\n",
            'fieldWithSingleQuotes' => 'Test\n\n',
            'fieldWithArray' => [
                'testDoubleQuotes' => "Test\n\n",
                'testSingleQuotes' => 'Test\n\n',
            ],
            'fieldWithObject' => (object) [
                'testDoubleQuotes' => "Test\n\n",
                'testSingleQuotes' => 'Test\n\n',
            ],
        ];
        $response = LogcomexLogger::treatContext($context, $exception);

        $this->assertIsArray($response);
        $this->assertEquals([
            'fieldWithDoubleQuotes',
            'fieldWithSingleQuotes',
            'fieldWithArray',
            'fieldWithObject',
            'exception-class',
            'message',
            'file',
            'line',
            'code'
        ], array_keys($response));
        array_walk_recursive($response, function ($value) {
            $this->assertDoesNotMatchRegularExpression('/\n/', $value);
            $this->assertIsNotObject($value);
        });
    }

    /**
     * @return void
     */
    public function testInfoWithContextArraySuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::info('test', ['opa']);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testInfoWithContextDtoSuccessFlow(): void
    {
        $fakeDto = new FakeDtoWithArrayable();
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::info('test', $fakeDto);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testInfoWithoutContextSuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::info('test');

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testErrorWithContextArraySuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::error('test', ['opa']);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testErrorWithContextDtoSuccessFlow(): void
    {
        $fakeDto = new FakeDtoWithArrayable();
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::error('test', $fakeDto);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testErrorWithoutContextSuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::error('test');

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testDebugWithContextArraySuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::debug('test', ['opa']);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testDebugWithContextDtoSuccessFlow(): void
    {
        $fakeDto = new FakeDtoWithArrayable();
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::debug('test', $fakeDto);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testDebugWithoutContextSuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::debug('test');

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testSevereWithContextArraySuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::severe('test', ['opa']);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testSevereWithContextDtoSuccessFlow(): void
    {
        $fakeDto = new FakeDtoWithArrayable();
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::severe('test', $fakeDto);

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testSevereWithoutContextSuccessFlow(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = Logger::severe('test');

        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testChannelExpectedInstanceType(): void
    {
        $response = Logger::channel('test');

        $this->assertInstanceOf(LogcomexLogger::class, $response);
    }

    /**
     * @return void
     */
    public function testChannelInfoAssertIfCreatingLogFileWithMessage(): void
    {
        $expectedLogMessageHash = md5('test' . time());
        Logger::channel('test')->info($expectedLogMessageHash);

        $this->assertLogContent($expectedLogMessageHash);
    }

    /**
     * @return void
     */
    public function testChannelErrorAssertIfCreatingLogFileWithMessage(): void
    {
        $expectedLogMessageHash = md5('test' . time());
        Logger::channel('test')->error($expectedLogMessageHash);

        $this->assertLogContent($expectedLogMessageHash);
    }

    /**
     * @return void
     */
    public function testChannelDebugAssertIfCreatingLogFileWithMessage(): void
    {
        $expectedLogMessageHash = md5('test' . time());
        Logger::channel('test')->debug($expectedLogMessageHash);

        $this->assertLogContent($expectedLogMessageHash);
    }

    /**
     * @return void
     */
    public function testChannelSevereAssertIfCreatingLogFileWithMessage(): void
    {
        $expectedLogMessageHash = md5('test' . time());
        Logger::channel('test')->severe($expectedLogMessageHash);

        $this->assertLogContent($expectedLogMessageHash);
    }

    /**
     * @return void
     */
    public function testInfoModePattern(): void
    {
        Logger::channel('test')->info('test', ['opa']);

        $this->assertLogContent('[[INFO]]');
    }

    /**
     * @return void
     */
    public function testDebugModePattern(): void
    {
        Logger::channel('test')->debug('test', ['opa']);

        $this->assertLogContent('[[DEBUG]]');
    }

    /**
     * @return void
     */
    public function testErrorModePattern(): void
    {
        Logger::channel('test')->error('test', ['opa']);

        $this->assertLogContent('[[ERROR]]');
    }

    /**
     * @return void
     */
    public function testSevereModePattern(): void
    {
        Logger::channel('test')->severe('test', ['opa']);

        $this->assertLogContent('[[SEVERE]]');
    }
}
