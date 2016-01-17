<?php

namespace sndsgd\http;

/**
 * A dictionary of http codes
 */
class Code
{
    /**
     * Status codes and their respective status texts
     * 
     * @var array<integer,string>
     */
    private static $codes = [
        # informational
        100 => "Continue",
        101 => "Switching Protocols",
        102 => "Processing",
        # success
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        # redirection
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        307 => "Temporary Redirect",
        # client error
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",
        428 => "Precondition Required",
        429 => "Too Many Requests",
        431 => "Request Header Fields Too Large",
        # server error
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported",
        507 => "Insufficient Storage",
        508 => "Loop Detected",
        509 => "Bandwidth Limit Exceeded",
        511 => "Network Authentication Required",
    ];

    /**
     * Get the relevant message for a status code
     *
     * @param int $statusCode The status code
     * @return string
     * @throws \InvalidArgumentException If the provided code is invalid
     */
    public static function getStatusText(int $statusCode): string
    {
        if (!array_key_exists($statusCode, static::$codes)) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'statusCode'; ".
                "expecting a valid status code as an integer"
            );
        }
        return static::$codes[$statusCode];
    }

    /**
     * Get a status group for a given status code (2xx, 3xx, etc)
     *
     * @param int $statusCode
     * @return string
     */
    public static function getStatusGroup(int $statusCode): string
    {
        if ($statusCode < 100 || $statusCode > 599) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'statusCode'; ".
                "expecting a valid status code as an integer"
            );
        }
        return floor($statusCode / 100)."xx";
    }
}
