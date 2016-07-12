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
        self::CONTINUE => "Continue",
        self::SWITCHING_PROTOCOLS => "Switching Protocols",
        self::PROCESSING => "Processing",
        self::OK => "OK",
        self::CREATED => "Created",
        self::ACCEPTED => "Accepted",
        self::NON_AUTHORITATIVE_INFORMATION => "Non-Authoritative Information",
        self::NO_CONTENT => "No Content",
        self::RESET_CONTENT => "Reset Content",
        self::PARTIAL_CONTENT => "Partial Content",
        self::MULTIPLE_CHOICES => "Multiple Choices",
        self::MOVED_PERMANENTLY => "Moved Permanently",
        self::FOUND => "Found",
        self::SEE_OTHER => "See Other",
        self::NOT_MODIFIED => "Not Modified",
        self::USE_PROXY => "Use Proxy",
        self::TEMPORARY_REDIRECT => "Temporary Redirect",
        self::BAD_REQUEST => "Bad Request",
        self::UNAUTHORIZED => "Unauthorized",
        self::PAYMENT_REQUIRED => "Payment Required",
        self::FORBIDDEN => "Forbidden",
        self::NOT_FOUND => "Not Found",
        self::METHOD_NOT_ALLOWED => "Method Not Allowed",
        self::NOT_ACCEPTABLE => "Not Acceptable",
        self::PROXY_AUTHENTICATION_REQUIRED => "Proxy Authentication Required",
        self::REQUEST_TIMEOUT => "Request Timeout",
        self::CONFLICT => "Conflict",
        self::GONE => "Gone",
        self::LENGTH_REQUIRED => "Length Required",
        self::PRECONDITION_FAILED => "Precondition Failed",
        self::REQUEST_ENTITY_TOO_LARGE => "Request Entity Too Large",
        self::REQUEST_URI_TOO_LONG => "Request-URI Too Long",
        self::UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
        self::REQUESTED_RANGE_NOT_SATISFIABLE => "Requested Range Not Satisfiable",
        self::EXPECTATION_FAILED => "Expectation Failed",
        self::PRECONDITION_REQUIRED => "Precondition Required",
        self::TOO_MANY_REQUESTS => "Too Many Requests",
        self::REQUEST_HEADER_FIELDS_TOO_LARGE => "Request Header Fields Too Large",
        self::INTERNAL_SERVER_ERROR => "Internal Server Error",
        self::NOT_IMPLEMENTED => "Not Implemented",
        self::BAD_GATEWAY => "Bad Gateway",
        self::SERVICE_UNAVAILABLE => "Service Unavailable",
        self::GATEWAY_TIMEOUT => "Gateway Timeout",
        self::HTTP_VERSION_NOT_SUPPORTED => "HTTP Version Not Supported",
        self::INSUFFICIENT_STORAGE => "Insufficient Storage",
        self::LOOP_DETECTED => "Loop Detected",
        self::BANDWIDTH_LIMIT_EXCEEDED => "Bandwidth Limit Exceeded",
        self::NETWORK_AUTHENTICATION_REQUIRED => "Network Authentication Required",
    ];

    /**
     * Determine whether a status code has a valid status message
     *
     * @param int $code The code to verify
     * @return bool
     */
    public static function isValid(int $code): bool
    {
        return isset(static::$statuses[$code]);
    }

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
        if (!static::isValid($code)) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'code'; ".
                "expecting a valid status code as an integer"
            );
        }
        return floor($code / 100)."xx";
    }
}
