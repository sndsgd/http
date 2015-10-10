<?php

namespace sndsgd\http\inbound;

use \Exception;
use \sndsgd\http\data\decoder\UrlDecoder;
use \sndsgd\http\inbound\request\exception\BadRequestException;
use \sndsgd\Str;
use \sndsgd\Url;


/**
 * An inbound request
 */
class Request
{
    /**
     * Request body decoders
     *
     * @var array<string,string>
     */
    protected static $dataTypes = [
        "application/json" => "sndsgd\\http\\data\\decoder\\JsonDecoder",
        "multipart/form-data" => "sndsgd\\http\\data\\decoder\\MultipartDataDecoder",
        "application/x-www-form-urlencoded" => "sndsgd\\http\\data\\decoder\\UrlDecoder",
    ];

    /**
     * The request method (GET, POST, PATCH, DELETE, ...)
     *
     * @var string
     */
    protected $method;

    /**
     * The uri path
     *
     * @var string
     */
    protected $path;

    /**
     * Once values are computed, they can be cached here
     *
     * @var array<string,mixed>
     */
    protected $cache = [];

    /**
     * Parameters included in the uri are stashed here after
     *
     * @var array<string,mixed>
     */
    protected $uriParameters;

    /**
     * Request query parameters are stashed here after they are decoded
     *
     * @var array<string,mixed>
     */
    protected $queryParameters;

    /**
     * Request body parameters are stashed here after they are decoded
     *
     * @var array<string,mixed>
     */
    protected $bodyParameters;

    /**
     * In some cases a response will be generated, and then stashed here
     *
     * @var \sndsgd\http\outbound\Response
     */
    protected $response;


    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    /**
     * @return string
     */
    public function getMethod()/*: string */
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()/*: string*/
    {
        return $this->path;
    }

    /**
     * @param string $name The name of the header to get
     * @param string $default A value to use if the header does not exist
     * @return string
     */
    public function getHeader(/*string*/ $name, /*string*/ $default = "")/*: string*/
    {
        $name = strtoupper($name);
        $name = "HTTP_".preg_replace("~[^A-Z0-9]~", "_", $name);
        return (array_key_exists($name, $_SERVER)) ? $_SERVER[$name] : $default;
    }

    /**
     * Get the content type
     *
     * @return string|null
     */
    public function getContentType()/*: string*/
    {
        $cacheKey = "content-type";
        if (!array_key_exists($cacheKey, $this->cache)) {
            $value = Str::before($this->getHeader("content-type", ""), ";");
            $this->cache[$cacheKey] = strtolower($value);
        }
        return $this->cache[$cacheKey];
    }

    /**
     * Get the accept content type
     *
     * @return string
     */
    public function getAcceptContentType()/*: string*/
    {
        # accept: text/html,application/xhtml+xml;q=0.9,image/webp,*/*;q=0.8
        $cacheKey = "accept-content-type";
        if (!array_key_exists($cacheKey, $this->cache)) {
            $value = Str::before($this->getHeader("accept", ""), ",");
            $this->cache[$cacheKey] = strtolower($value);
        }
        return $this->cache[$cacheKey];
    }

    /**
     * Get the basic auth credentials
     *
     * @return array<string|null>
     */
    public function getBasicAuth()/*: array*/
    {
        $cacheKey = "basic-auth";
        if (!array_key_exists("basic-auth", $this->cache)) {
            $this->cache[$cacheKey] = [
                array_key_exists("PHP_AUTH_USER", $_SERVER)
                    ? $_SERVER["PHP_AUTH_USER"] : null,
                array_key_exists("PHP_AUTH_PW", $_SERVER)
                    ? $_SERVER["PHP_AUTH_PW"] : null,
            ];
        }
        return $this->cache[$cacheKey];
    }

    /**
     * @param array<string,mixed> $params
     */
    public function setUriParameters(array $params)
    {
        $this->uriParameters = $params;
    }

    /**
     * @return array<string,mixed>
     */
    public function getUriParameters()/*: array*/
    {
        return $this->uriParameters;
    }

    /**
     * @return array<string,mixed>
     */
    public function getQueryParameters()/*: array*/
    {
        if ($this->queryParameters === null) {
            $result = [];
            $pos = strpos($_SERVER["REQUEST_URI"], "?");
            if ($pos !== false) {
                $queryString = substr($_SERVER["REQUEST_URI"], $pos + 1);
                $rfc = UrlDecoder::getRfc();
                $result = Url::decodeQueryString($queryString, $rfc);
            }
            $this->queryParameters = $result;
        }
        return $this->queryParameters;
    }

    /**
     * Get the request data using the content type
     *
     * @return array
     * @throws Exception If the provided content type is not acceptable
     */
    public function getBodyParameters()/*: array*/
    {
        if ($this->bodyParameters === null) {
            $contentType = $this->getContentType();
            if (!array_key_exists($contentType, static::$dataTypes)) {
                throw new BadRequestException(
                    "Unknown Content-Type '$contentType'"
                );
            }

            $class = static::$dataTypes[$contentType];
            $decoder = new $class;
            $this->bodyParameters = $decoder->getDecodedData();
        }
        return $this->bodyParameters;
    }

    /**
     * Attempt to get the raw request body
     *
     * @return string
     */
    public function getRawBody()
    {
        return file_get_contents("php://input");
    }
}
