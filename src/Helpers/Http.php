<?php

namespace Consul\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Http\Client\Response;
use Consul\Exceptions\RequestException;

/**
 * Class Http
 *
 * @package Consul\Helpers
 */
class Http
{
    /**
     * HTTP status code to message map array
     * @var array|string[]
     */
    private static array $codeToMessageMap = [
        200   =>  'OK',
        201   =>  'Created',
        202   =>  'Accepted',
        203   =>  'Non-Authoritative Information',
        204   =>  'No Content',
        205   =>  'Reset Content',
        206   =>  'Partial Content',
        207   =>  'Multi-Status',
        208   =>  'Already Reported',
        226   =>  'IM Used',
        300   =>  'Multiple Choices',
        301   =>  'Moved Permanently',
        302   =>  'Found',
        303   =>  'See Other',
        304   =>  'Not Modified',
        305   =>  'Use Proxy',
        306   =>  'Switch Proxy',
        307   =>  'Temporary Redirect',
        308   =>  'Permanent Redirect',
        400   =>  'Bad Request',
        401   =>  'Unauthorized',
        402   =>  'Payment Required',
        403   =>  'Forbidden',
        404   =>  'Not Found',
        405   =>  'Method Not Allowed',
        406   =>  'Not Acceptable',
        407   =>  'Proxy Authentication Required',
        408   =>  'Request Timeout',
        409   =>  'Conflict',
        410   =>  'Gone',
        411   =>  'Length Required',
        412   =>  'Precondition Failed',
        413   =>  'Payload Too Large',
        414   =>  'URI Too Long',
        415   =>  'Unsupported Media Type',
        416   =>  'Range Not Satisfiable',
        417   =>  'Expectation Failed',
        418   =>  'I\'m a teapot',
        421  =>  'Misdirected Request',
        422   =>  'Unprocessable Entity',
        423   =>  'Locked',
        424   =>  'Failed Dependency',
        425   =>  'Too Early',
        426   =>  'Upgrade Required',
        428   =>  'Precondition Required',
        429   =>  'Too Many Requests',
        431   =>  'Request Header Fields Too Large',
        451   =>  'Unavailable For Legal Reasons',
        500   =>  'Internal Server Error',
        501   =>  'Not Implemented',
        502   =>  'Bad Gateway',
        503   =>  'Service Unavailable',
        504   =>  'Gateway Timeout',
        505   =>  'HTTP Version Not Supported',
        506   =>  'Variant Also Negotiates',
        507   =>  'Insufficient Storage',
        508   =>  'Loop Detected',
        510   =>  'Not Extended',
        511   =>  'Network Authentication Required',
    ];

    /**
     * Convert status code to message
     * @param int $statusCode
     *
     * @return string
     */
    public static function statusCodeToMessage(int $statusCode): string
    {
        return self::$codeToMessageMap[$statusCode];
    }

    /**
     * Create new exception from status code
     * @param int $statusCode
     *
     * @return RequestException
     */
    public static function statusCodeToException(int $statusCode): RequestException
    {
        return new RequestException(self::statusCodeToMessage($statusCode), $statusCode);
    }

    /**
     * Create new exception from request
     * @param Response $response
     *
     * @return RequestException
     */
    public static function responseToException(Response $response): RequestException
    {
        $statusMessage = self::statusCodeToMessage($response->status());
        $requestUrl = Arr::get($response->handlerStats(), 'url');
        $exceptionBody = $response->body();
        return new RequestException(
            statusCode: $response->status(),
            statusMessage: $statusMessage,
            responseMessage: $exceptionBody,
            requestUrl: $requestUrl
        );
    }
}
