<?php

namespace Logcomex\PhpUtils\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Enumerators\LogEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Facades\Logger;
use Logcomex\PhpUtils\Singletons\TracerSingleton;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;


/**
 * Class HttpHelper
 * @package Logcomex\PhpUtils\Helpers
 * @method ResponseInterface get(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface head(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface put(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface post(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface patch(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface delete(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface getAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface headAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface putAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface postAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface patchAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface deleteAsync(string|UriInterface $uri, array $options = [])
 */
class HttpHelper
{
    /**
     * @var bool
     */
    private static $mustReturnError = false;

    /**
     * @var int
     */
    private static $expectedHttpErrorCode = 400;

    /**
     * @var array
     */
    private $clientConfig;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private static $expectedHttpErrorCode = 400;

    /**
     * @var array
     */
    private $mockedEndpoints;

    /**
     * HttpHelper constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->clientConfig = $config;
        $this->client = new Client($config);
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

        Logger::info(
            LogEnum::REQUEST_HTTP_OUT,
            ['http_url_request_out' => $urlPath, 'payload' => $args,]
        );

        // Tratativa criada pra endpoint mockados,
        // se não estiver registro no contrato de mocks,
        // será feita a requisição normalmente.
        if ($this->isTestMode()) {
            try {
                if (!$this->isMockedEndpoint($urlPath)) {
                    throw new BadImplementationException(
                        ErrorEnum::PHU003,
                        'You are requesting to external APIs in test mode. Please mock your endpoint.'
                    );
                }
                if (!class_exists($this->mockedEndpoints[$urlPath])) {
                    throw new BadImplementationException(
                        ErrorEnum::PHU004,
                        'Mock Class registered does not exist.'
                    );
                }

                $mockResponse = call_user_func_array([$this->mockedEndpoints[$urlPath], 'mock'], []);

                $httpCodeResponse = self::$mustReturnError ? self::$expectedHttpErrorCode : 200;
                $mock = new MockHandler([
                    new Response($httpCodeResponse, [], $mockResponse),
                ]);

                $handlerStack = HandlerStack::create($mock);
                $clientMock = new Client(array_merge(['handler' => $handlerStack], $this->clientConfig));
                $clientMock = new Client(['handler' => $handlerStack]);

                return $clientMock->request('GET', '/');
            } finally {
                self::$expectedHttpErrorCode = 400;
            }
        }

        return $this->client->{$method}(...$args);
        $client = new Client();

        return $client->{$method}(...$args);
    }

    /**
     * @param int $httpCode
     * @return void
     */
    public static function mustReturnError(int $httpCode = 400): void
    {
        self::$mustReturnError = true;
        self::$expectedHttpErrorCode = $httpCode;
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

        foreach ($functionArguments as &$functionArgument) {
            if (!is_array($functionArgument)) {
               continue;
            }

            if (in_array(RequestOptions::HEADERS, array_keys($functionArgument))) {
                $headers = $functionArgument[RequestOptions::HEADERS];
                $headers = array_merge($headers, $tracerHeaderToPropagate);

                $functionArgument[RequestOptions::HEADERS] = $headers;
                continue;
            }

            $functionArgument[RequestOptions::HEADERS] = $tracerHeaderToPropagate;
        }

        return $functionArguments;
    }
}
