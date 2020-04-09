<?php

namespace Logcomex\PhpUtils\Logs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class RequestLog
 * @package Logcomex\PhpUtils\Logs
 */
class RequestLog implements Arrayable, Jsonable
{
    /**
     * @var
     */
    private $requestHeaders;
    /**
     * @var
     */
    private $requestServer;
    /**
     * @var
     */
    private $requestPayload;
    /**
     * @var
     */
    private $responseHeaders;
    /**
     * @var
     */
    private $responseContent;
    /**
     * @var
     */
    private $responseTime;

    /**
     * @param mixed $requestHeaders
     * @return RequestLog
     */
    public function setRequestHeaders($requestHeaders)
    {
        $this->requestHeaders = $requestHeaders;

        return $this;
    }

    /**
     * @param mixed $requestServer
     * @return RequestLog
     */
    public function setRequestServer($requestServer)
    {
        $this->requestServer = $requestServer;

        return $this;
    }

    /**
     * @param mixed $requestPayload
     * @return RequestLog
     */
    public function setRequestPayload($requestPayload)
    {
        $this->requestPayload = $requestPayload;

        return $this;
    }

    /**
     * @param mixed $responseHeaders
     * @return RequestLog
     */
    public function setResponseHeaders($responseHeaders)
    {
        $this->responseHeaders = $responseHeaders;

        return $this;
    }

    /**
     * @param mixed $responseContent
     * @return RequestLog
     */
    public function setResponseContent($responseContent)
    {
        $this->responseContent = $responseContent;

        return $this;
    }

    /**
     * @param mixed $responseTime
     * @return RequestLog
     */
    public function setResponseTime($responseTime)
    {
        $this->responseTime = $responseTime;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'request' => [
                'headers' => $this->requestHeaders,
                'server' => $this->requestServer,
                'payload' => $this->requestPayload,
            ],
            'response' => [
                'headers' => $this->responseHeaders,
                'content' => $this->responseContent,
                'execution-time' => $this->responseTime,
            ]
        ];
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
