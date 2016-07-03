<?php

namespace sndsgd\http;

use \sndsgd\http\data\decoder;
use \sndsgd\http\exception;

class Request
{
    /**
     * A copy of the $_SERVER superglobal
     *
     * @var array<string,string|integer|float>
     */
    protected $server;

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
     * @var string
     */
    protected $contentType;

    /**
     * @var array<string,string>
     */
    protected $acceptContentTypes;

    /**
     * The decoded querystring
     *
     * @var array<string,mixed>
     */
    protected $query;

    /**
     * The decoded request body parameters
     *
     * @var array
     */
    protected $bodyParameters;

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
        return $this->server["REQUEST_METHOD"] ?? "GET";
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        if (!$this->path) {
            if (isset($this->server["REQUEST_URI"])) {
                $path = parse_url($this->server["REQUEST_URI"], PHP_URL_PATH);
                $this->path = rawurldecode($path);
            }
            else {
                $this->path = "/";
            }
        }
        return $this->path;
    }

    /**
     * Get the request protocol
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->server["SERVER_PROTOCOL"] ?? "HTTP/1.1";
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
     * Get the remote ip address
     *
     * @return string
     */
    public function getIp(): string
    {
        foreach (["HTTP_X_FORWARDED_FOR", "X_FORWARDED_FOR"] as $key) {
            if (isset($this->server[$key])) {
                return $this->server[$key];
            }
        }
        return $this->server["REMOTE_ADDR"] ?? "";
    }

    /**
     * @param string $name The name of the header to get
     * @param string $default A value to use if the header does not exist
     * @return string
     */
    public function getHeader(string $name, string $default = ""): string
    {
        if (!$this->headers) {
            $this->headers = $this->readHeaders();
        }
        $name = strtolower($name);
        return $this->headers[$name] ?? $default;
    }

    /**
     * @return array<string,string>
     */
    public function getHeaders(): array
    {
        if ($this->headers === null) {
            $this->headers = $this->readHeaders();
        }
        return $this->headers;
    }

    /**
     * Create an array of all headers in the
     *
     * @return array<string,string>
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
     * @return string
     */
    public function getContentType(): string
    {
        if ($this->contentType === null) {
            $header = strtolower($this->getHeader("content-type", ""));
            $this->contentType = \sndsgd\Str::before($header, ";");
        }
        return $this->contentType;
    }

    /**
     * @return int
     */
    public function getContentLength(): int
    {
        return (int) $this->getHeader("content-length", "0");
    }

    /**
     * Get client specified acceptable content types
     *
     * @return array<string,string>
     */
    public function getAcceptContentTypes(): array
    {
        if ($this->acceptContentTypes === null) {
            $this->acceptContentTypes = [];
            $header = $this->getHeader("accept", "");
            if ($header !== "") {
                $pos = strpos($header, ";");
                if ($pos !== false) {
                    $header = substr($header, 0, $pos);
                }
                foreach (explode(",", $header) as $type) {
                    $type = strtolower($type);
                    $this->acceptContentTypes[$type] = $type;
                }
            }
        }
        return $this->acceptContentTypes;
    }

    /**
     * Get the basic auth credentials
     *
     * @return array<string>
     */
    public function getBasicAuth(): array
    {
        return [
            ($this->server["PHP_AUTH_USER"] ?? ""),
            ($this->server["PHP_AUTH_PW"] ?? ""),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function getQueryParameters(): array
    {
        if ($this->query === null) {
            if (
                isset($this->server["QUERY_STRING"]) &&
                $this->server["QUERY_STRING"] !== ""
            ) {
                $decoder = new decoder\QueryStringDecoder(0);
                $this->query = $decoder->decode($this->server["QUERY_STRING"]);
            } else {
                $this->query = [];
            }
        }
        return $this->query;
    }

    /**
     * Stubbable method for creating a body decoder
     *
     * @return \sndsgd\http\request\BodyDecoder
     */
    protected function getBodyDecoder()
    {
        return new request\BodyDecoder();
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
            $contentType = $this->getHeader("content-type", "");
            if ($contentType === "") {
                $this->bodyParameters = [];
            } else {
                $decoder = $this->getBodyDecoder();
                $this->bodyParameters = $decoder->decode(
                    $this->getMethod(),
                    "php://input",
                    $contentType,
                    $this->getContentLength()
                );
            }
        }
        return $this->bodyParameters;
    }    
}
