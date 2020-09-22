<?php

namespace Logcomex\PhpUtils\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Singletons\TracerSingleton;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpHelper
 * @package Logcomex\PhpUtils\Helpers
 */
class HttpHelper
{
    /**
     * @var bool
     */
    private static $mustReturnError = false;
    /**
     * @var array
     */
    private $mockedEndpoints;

    /**
     * HttpHelper constructor.
     */
    public function __construct()
    {
        $this->mockedEndpoints = config('mockedEndpoints') ?? [];
    }

    /**
     * @param $method
     * @param $args
     * @return ResponseInterface
     * @throws BadImplementationException
     * @throws GuzzleException
     */
    public function __call($method, $args)
    {
        $urlPath = parse_url($args[0] ?? '')['path'];

        if (!empty($tracerValue = TracerSingleton::getTraceValue())) {
            $args = self::propagateTracerValue($tracerValue, $args);
        }

        // Tratativa criada pra endpoint mockados,
        // se não estiver registro no contrato de mocks,
        // será feita a requisição normalmente.
        if ($this->isTestMode()) {
            if (!$this->isMockedEndpoint($urlPath)) {
                throw new BadImplementationException(
                    'PHU-003',
                    'You are requesting to external APIs in test mode. Please mock your endpoint.'
                );
            }
            if (!class_exists($this->mockedEndpoints[$urlPath])) {
                throw new BadImplementationException(
                    'PHU-004',
                    'Mock Class registered does not exists.'
                );
            }

            $mockResponse = call_user_func_array([$this->mockedEndpoints[$urlPath], 'mock'], []);

            $httpCodeResponse = self::$mustReturnError ? 400 : 200;
            $mock = new MockHandler([
                new Response($httpCodeResponse, [], $mockResponse),
            ]);

            $handlerStack = HandlerStack::create($mock);
            $clientMock = new Client(['handler' => $handlerStack]);

            return $clientMock->request('GET', '/');
        }

        $client = new Client();
        return $client->request($method, ...$args);
    }

    /**
     * @return void
     */
    public static function mustReturnError(): void
    {
        self::$mustReturnError = true;
    }

    /**
     * @return void
     */
    public static function mustNotReturnError(): void
    {
        self::$mustReturnError = false;
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return preg_match('/test/', config('app.mode'));
    }

    /**
     * @param string $endpoint
     * @return bool
     */
    public function isMockedEndpoint(string $endpoint): bool
    {
        return !empty($endpoint) && array_key_exists($endpoint, $this->mockedEndpoints);
    }

    /**
     * @param string $tracerValue
     * @param $functionArguments
     * @return array
     */
    public static function propagateTracerValue(string $tracerValue, $functionArguments): array
    {
        $headersNamesToPropagate = config('tracer.headersToPropagate');
        $headersNamesToPropagate = is_array($headersNamesToPropagate)
            ? $headersNamesToPropagate
            : [$headersNamesToPropagate];

        $tracerHeaderToPropagate = [];
        foreach ($headersNamesToPropagate as $headerNameToPropagate) {
            $tracerHeaderToPropagate[strtolower($headerNameToPropagate)] = $tracerValue;
        }

        $hasRequestOptions = count($functionArguments) > 1;
        $functionArguments[1][RequestOptions::HEADERS] = $hasRequestOptions
            ? array_merge($functionArguments[1][RequestOptions::HEADERS] ?? [], $tracerHeaderToPropagate)
            : $tracerHeaderToPropagate;

        return $functionArguments;
    }
}
