<?php

namespace sndsgd\http;

use \sndsgd\http\data\decoder;

class Request implements RequestInterface
{
    /**
     * The request environment
     *
     * @var \sndsgd\Environment|array
     */
    protected $environment;

    /**
     * When a host instance is created, it will be cached here
     *
     * @var \sndsgd\http\request\HostInterface
     */
    protected $host;

    /**
     * When a client instance is created, it will be cached here
     *
     * @var \sndsgd\http\request\ClientInterface
     */
    protected $client;

    /**
     * The uri path
     *
     * @var string
     */
    protected $path;

    /**
     * Headers are parsed and cached here
     *
     * @var array<string,string>
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
     * The decoded query parameters
     *
     * @var array<string,mixed>
     */
    protected $queryParameters;

    /**
     * The decoded request body parameters
     *
     * @var array
     */
    protected $bodyParameters;

    /**
     * Create a request instance
     *
     * @param \sndsgd\Environment $environment
     */
    public function __construct(\sndsgd\Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment(): \sndsgd\Environment
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): \sndsgd\http\request\HostInterface
    {
        if ($this->host === null) {
            $this->host = new \sndsgd\http\request\Host($this);
        }
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient(): \sndsgd\http\request\ClientInterface
    {
        if ($this->client === null) {
            $this->client = new \sndsgd\http\request\Client($this);
        }
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->environment["REQUEST_METHOD"] ?? "GET";
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        if ($this->path === null) {
            if (isset($this->environment["REQUEST_URI"])) {
                $path = parse_url($this->environment["REQUEST_URI"], PHP_URL_PATH);
                $this->path = rawurldecode($path);
            } else {
                $this->path = "/";
            }
        }
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocol(): string
    {
        return $this->environment["SERVER_PROTOCOL"] ?? "HTTP/1.1";
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        # commonly provided by load balancers
        if (isset($this->environment["HTTP_X_FORWARDED_PROTO"])) {
            return $this->environment["HTTP_X_FORWARDED_PROTO"];
        }

        # allow for setting `fastcgi_param HTTPS on;` in nginx config
        if (isset($this->environment["HTTPS"])) {
            return "https";
        }

        # fallback to using the port
        $port = $this->environment["SERVER_PORT"] ?? 80;
        if ($port == 443) {
            return "https";
        }

        return "http";
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(string $name, string $default = ""): string
    {
        $lowercaseName = strtolower($name);
        $headers = $this->getHeaders();
        return $headers[$lowercaseName] ?? $default;
    }

    /**
     * {@inheritdoc}
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
    protected function readHeaders()
    {
        $ret = [];
        foreach ($this->environment as $key => $value) {
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getContentLength(): int
    {
        return (int) $this->getHeader("content-length", "0");
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getBasicAuth(): array
    {
        return [
            ($this->environment["PHP_AUTH_USER"] ?? ""),
            ($this->environment["PHP_AUTH_PW"] ?? ""),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParameters(): array
    {
        if ($this->queryParameters === null) {
            if (
                isset($this->environment["QUERY_STRING"]) &&
                $this->environment["QUERY_STRING"] !== ""
            ) {
                $decoder = new decoder\QueryStringDecoder(0);
                $this->queryParameters = $decoder->decode(
                    $this->environment["QUERY_STRING"]
                );
            } else {
                $this->queryParameters = [];
            }
        }
        return $this->queryParameters;
    }

    /**
     * {@inheritdoc}
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

    /**
     * Stubbable method for creating a body decoder
     *
     * @return \sndsgd\http\request\BodyDecoder
     */
    protected function getBodyDecoder(): request\BodyDecoder
    {
        return new request\BodyDecoder();
    }
}
