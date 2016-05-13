<?php

namespace sndsgd\http;

class Status
{
    # informational
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
    const PROCESSING = 102;
    # success
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NON_AUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    # redirection
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const TEMPORARY_REDIRECT = 307;
    # client error
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const REQUEST_URI_TOO_LONG = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const PRECONDITION_REQUIRED = 428;
    const TOO_MANY_REQUESTS = 429;
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    # server error
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const INSUFFICIENT_STORAGE = 507;
    const LOOP_DETECTED = 508;
    const BANDWIDTH_LIMIT_EXCEEDED = 509;
    const NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * Http status codes and their respective messages
     * 
     * @var array<integer,string>
     */
    protected static $statuses = [
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
     * Get a message given a status code
     *
     * @param int $code The status code
     * @return string
     * @throws \InvalidArgumentException If the provided code is invalid
     */
    public static function getText(int $code): string
    {
        if (!isset(static::$statuses[$code])) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'code'; ".
                "expecting a valid status code as an integer"
            );
        }
        return static::$statuses[$code];
    }

    /**
     * Get a status group for a given status code (2xx, 3xx, etc)
     *
     * @param int $code
     * @return string
     */
    public static function getGroup(int $code): string
    {
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'code'; ".
                "expecting a valid status code as an integer"
            );
        }
        return floor($code / 100)."xx";
    }
}
