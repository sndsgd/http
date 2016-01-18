<?php

namespace sndsgd\http\inbound;

use \Exception;
use \sndsgd\http\data\decoder\QueryStringDecoder;
use \sndsgd\http\inbound\request\exception\BadRequestException;
use \sndsgd\Str;

/**
 * An inbound request
 */
class Request
{
    const CACHE_KEY_CONTENT_TYPE = "header-content-type";
    const CACHE_KEY_ACCEPT = "header-accept";
    const CACHE_KEY_BASIC_AUTH = "basic-auth";

    /**
     * A copy of the $_SERVER superglobal
     *
     * @var array<string,string|integer|float>
     */
    protected $server;

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
     * Headers are parsed and cached here
     *
     * @var array<string>
     */
    protected $headers;

    /**
     * The decoded querystring
     *
     * @var array<string,mixed>
     */
    protected $query;

    /**
     * Once values are computed, they can be cached here
     *
     * @var array<string,mixed>
     */
    protected $cache = [];

    /**
     * Create a request instance
     *
     * @param array $server The PHP $_SERVER superglobal
     */
    public function __construct(array $server)
    {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        if ($this->method === null) {
            $this->method = isset($this->server["REQUEST_METHOD"]) 
                ? $this->server["REQUEST_METHOD"]
                : "GET";
        }
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        if ($this->path === null) {
            $this->path = isset($this->server["REQUEST_URI"])
                ? parse_url($this->server["REQUEST_URI"], PHP_URL_PATH)
                : "/";
        }
        return $this->path;
    }

    /**
     * Get the host that is handling this request
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->server["HTTP_HOST"] ?? "";
    }

    /**
     * @return array<string,mixed>
     */
    public function getQuery(): array
    {
        if ($this->query === null) {
            if (isset($this->server["QUERY_STRING"])) {
                $this->query = (new QueryStringDecoder(0))
                    ->decode($this->server["QUERY_STRING"])
                    ->getValues();    
            }
            else {
                $this->query = [];
            }
        }
        return $this->query;
    }

    /**
     * @param string $name The name of the header to get
     * @param string $default A value to use if the header does not exist
     * @return string
     */
    public function getHeader(string $name, string $default = ""): string
    {
        if ($this->headers === null) {
            $this->headers = $this->readHeaders();
        }
        return isset($this->headers[$name]) ? $this->headers[$name]: $default;
    }

    /**
     * Create an array of all headers in the 
     *
     * @return array<string,string|integer>
     */
    private function readHeaders()
    {
        $ret = [];
        foreach ($this->server as $key => $value) {
            # Note: the content-type and content-length headers always come
            # after the `CONTENT_TYPE` and `CONTENT_LENGTH` values in the
            # $_SERVER superglobal; if you use the values for the non `HTTP_...`
            # version, they will just be overwritten by the `HTTP_` version.
            if (strpos($key, "HTTP_") === 0) {
                $key = substr($key, 5);
                $key = strtolower($key);
                $key = str_replace("_", "-", $key);
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * Get the content type
     *
     * @return string|null
     */
    public function getContentType(): string
    {
        if (!isset($this->cache[static::CACHE_KEY_CONTENT_TYPE])) {
            $value = Str::before($this->getHeader("content-type", ""), ";");
            $this->cache[static::CACHE_KEY_CONTENT_TYPE] = strtolower($value);
        }
        return $this->cache[static::CACHE_KEY_CONTENT_TYPE];
    }

    /**
     * Get the accept content type
     *
     * @return string
     */
    public function getAcceptContentType(): string
    {
        # accept: text/html,application/xhtml+xml;q=0.9,image/webp,*/*;q=0.8
        if (!isset($this->cache[static::CACHE_KEY_ACCEPT])) {
            $value = Str::before($this->getHeader("accept", ""), ",");
            $this->cache[static::CACHE_KEY_ACCEPT] = strtolower($value);
        }
        return $this->cache[static::CACHE_KEY_ACCEPT];
    }

    /**
     * Get the basic auth credentials
     *
     * @return array<string|null>
     */
    public function getBasicAuth(): array
    {
        if (!isset($this->cache[static::CACHE_KEY_BASIC_AUTH])) {
            $this->cache[static::CACHE_KEY_BASIC_AUTH] = [
                ($this->server["PHP_AUTH_USER"] ?? ""),
                ($this->server["PHP_AUTH_PW"] ?? ""),
            ];
        }
        return $this->cache[static::CACHE_KEY_BASIC_AUTH];
    }

    /**
     * Get the request data using the content type
     *
     * @return array
     * @throws Exception If the provided content type is not acceptable
     */
    public function getBodyParameters(): array
    {
        if ($this->bodyParameters === null) {
            $this->decodeBody();
        }
        return $this->bodyParameters;
    }

    public function getBodyParameter(string $name, $default = null)
    {
        if ($this->bodyParameters === null) {
            $this->decodeBody();
        }
        return (array_key_exists($name, $this->bodyParameters))
            ? $this->bodyParameters[$name]
            : $default;
    }

    private function decodeBody()
    {
        $contentType = $this->getContentType();
        if (!array_key_exists($contentType, static::$contentTypes)) {
            throw new BadRequestException("unknown content-type '$contentType'");
        }

        $class = static::$contentTypes[$contentType];
        $decoder = new $class;
        $this->bodyParameters = $decoder->getDecodedData();
    }
}
