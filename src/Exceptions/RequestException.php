<?php

namespace Consul\Exceptions;

use Exception;
use Throwable;

/**
 * Class RequestException
 *
 * @package Consul\Exceptions
 */
class RequestException extends Exception
{
    /**
     * Status code returned by the request
     * @var int
     */
    private int $statusCode;

    /**
     * Response code converted to HTTP message
     * @var string
     */
    private string $statusMessage;

    /**
     * Previous exception (if any)
     * @var Throwable|null
     */
    private ?Throwable $previousException;

    /**
     * Request URL
     * @var string|null
     */
    private ?string $requestUrl;

    /**
     * Message returned by the response itself
     * @var string
     */
    private string $responseMessage;

    /**
     * RequestException Constructor.
     *
     * @param string         $statusMessage
     * @param int            $statusCode
     * @param Throwable|null $previousException
     * @param string         $responseMessage
     * @param string|null    $requestUrl
     */
    public function __construct(
        string $statusMessage = '',
        int $statusCode = 0,
        ?Throwable $previousException = null,
        string $responseMessage = '',
        ?string $requestUrl = null,
    ) {
        $this->statusMessage = $statusMessage;
        $this->statusCode = $statusCode;
        $this->previousException = $previousException;
        $this->responseMessage = $responseMessage;
        $this->requestUrl = $requestUrl;
        parent::__construct($this->generateExceptionMessage(), $statusCode, $previousException);
    }

    /**
     * Get status code
     * @return int
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get status message
     * @return string
     */
    public function statusMessage(): string
    {
        return $this->statusMessage;
    }

    /**
     * Get response message
     * @return string
     */
    public function responseMessage(): string
    {
        return $this->responseMessage;
    }

    /**
     * Get request URL
     * @return string|null
     */
    public function requestUrl(): ?string
    {
        return $this->requestUrl;
    }

    /**
     * Get previous exception
     * @return Throwable|null
     */
    public function previousException(): ?Throwable
    {
        return $this->previousException;
    }

    /**
     * Generate exception message
     * @return string
     */
    private function generateExceptionMessage(): string
    {
        if ($this->requestUrl() !== null) {
            return sprintf(
                '%s exception occurred while tried to access `%s`. Response is - %s',
                $this->statusMessage(),
                $this->requestUrl(),
                $this->responseMessage()
            );
        }

        return sprintf(
            '%s exception occurred, response message is - %s',
            $this->statusMessage(),
            $this->responseMessage()
        );
    }
}
