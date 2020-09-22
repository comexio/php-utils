<?php

namespace Logcomex\PhpUtils\Logs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class RequestInfoLog
 * @package Logcomex\PhpUtils\Logs
 */
class RequestInfoLog implements Arrayable, Jsonable
{
    /**
     * @var array
     */
    private $requestHeaders;
    /**
     * @var array
     */
    private $requestServer;
    /**
     * @var array
     */
    private $requestPayload;
    /**
     * @var array
     */
    private $responseHeaders;
    /**
     * @var array
     */
    private $responseContent;
    /**
     * @var float
     */
    private $responseTime;
    /**
     * @var string
     */
    private $traceId;

    /**
     * @param mixed $requestHeaders
     * @return RequestInfoLog
     */
    public function setRequestHeaders($requestHeaders): RequestInfoLog
    {
        $this->requestHeaders = $requestHeaders;

        return $this;
    }

    /**
     * @param mixed $requestServer
     * @return RequestInfoLog
     */
    public function setRequestServer($requestServer): RequestInfoLog
    {
        $this->requestServer = $requestServer;

        return $this;
    }

    /**
     * @param mixed $requestPayload
     * @return RequestInfoLog
     */
    public function setRequestPayload($requestPayload): RequestInfoLog
    {
        $this->requestPayload = $requestPayload;

        return $this;
    }

    /**
     * @param mixed $responseHeaders
     * @return RequestInfoLog
     */
    public function setResponseHeaders($responseHeaders): RequestInfoLog
    {
        $this->responseHeaders = $responseHeaders;

        return $this;
    }

    /**
     * @param mixed $responseContent
     * @return RequestInfoLog
     */
    public function setResponseContent($responseContent): RequestInfoLog
    {
        $this->responseContent = $responseContent;

        return $this;
    }

    /**
     * @param mixed $responseTime
     * @return RequestInfoLog
     */
    public function setResponseTime($responseTime): RequestInfoLog
    {
        $this->responseTime = $responseTime;

        return $this;
    }

    /**
     * @param string $traceId
     * @return RequestInfoLog
     */
    public function setTraceId(string $traceId): RequestInfoLog
    {
        $this->traceId = $traceId;

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
                'trace-id' => $this->traceId,
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
