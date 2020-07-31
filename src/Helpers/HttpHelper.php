<?php

namespace Logcomex\PhpUtils\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
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
     */
    public function __call($method, $args)
    {
        $urlPath = parse_url($args[0] ?? '')['path'];

        // Tratativa criada pra endpoint mockados,
        // se não estiver registro no contrato de mocks,
        // será feita a requisição normalmente.
        if ($this->isTestMode()) {
            if (!$this->isMockedEndpoint($urlPath)) {
                throw new BadImplementationException('You are requesting to external APIs in test mode. Please mock your endpoint.');
            }
            if (!class_exists($this->mockedEndpoints[$urlPath])) {
                throw new BadImplementationException('Mock Class registered does not exists.');
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
}
