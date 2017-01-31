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
     * The decoder options use for query, body, and header decoding
     *
     * @var \sndsgd\http\data\decoder\DecoderOptions
     */
    protected $decoderOptions;

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
     * Once cookies are processed, they'll be cached here
     *
     * @var array<string,mixed>
     */
    protected $cookies;

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
     * @param \sndsgd\http\data\decoder\DecoderOptions|null $decoderOptions
     */
    public function __construct(
        \sndsgd\Environment $environment,
        \sndsgd\http\data\decoder\DecoderOptions $decoderOptions = null
    )
    {
        $this->environment = $environment;
        $this->decoderOptions = $decoderOptions ?? new data\decoder\DecoderOptions();
    }

    /**
     * @inheritDoc
     */
    public function getEnvironment(): \sndsgd\Environment
    {
        return $this->environment;
    }

    /**
     * @inheritDoc
     */
    public function getDecoderOptions(): \sndsgd\http\data\decoder\DecoderOptions
    {
        return $this->decoderOptions;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): \sndsgd\http\request\HostInterface
    {
        if ($this->host === null) {
            $this->host = new \sndsgd\http\request\Host($this);
        }
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getClient(): \sndsgd\http\request\ClientInterface
    {
        if ($this->client === null) {
            $this->client = new \sndsgd\http\request\Client($this);
        }
        return $this->client;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->environment["REQUEST_METHOD"] ?? "GET";
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getProtocol(): string
    {
        return $this->environment["SERVER_PROTOCOL"] ?? "HTTP/1.1";
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        # commonly provided by load balancers
        if (isset($this->environment["HTTP_X_FORWARDED_PROTO"])) {
            $scheme = strtolower($this->environment["HTTP_X_FORWARDED_PROTO"]);
            if (in_array($scheme, [Scheme::HTTP, Scheme::HTTPS])) {
                return $scheme;
            }
        }

        # allow for setting `fastcgi_param HTTPS on;` in nginx config
        if (isset($this->environment["HTTPS"])) {
            return Scheme::HTTPS;
        }

        # fallback to using the port
        $port = $this->environment["SERVER_PORT"] ?? 80;
        if ($port == 443) {
            return Scheme::HTTPS;
        }

        return Scheme::HTTP;
    }

    /**
     * @inheritDoc
     */
    public function isHttps(): bool
    {
        return ($this->getScheme() === Scheme::HTTPS);
    }

    /**
     * @inheritDoc
     */
    public function getHeader(string $name, string $default = ""): string
    {
        $lowercaseName = strtolower($name);
        $headers = $this->getHeaders();
        return $headers[$lowercaseName] ?? $default;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getContentLength(): int
    {
        return (int) $this->getHeader("content-length", "0");
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getBasicAuth(): array
    {
        return [
            ($this->environment["PHP_AUTH_USER"] ?? ""),
            ($this->environment["PHP_AUTH_PW"] ?? ""),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCookies(): array
    {
        if ($this->cookies === null) {
            $this->cookies = $this->parseCookies();
        }
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function getCookie(string $name, $default = "")
    {
        if ($this->cookies === null) {
            $this->cookies = $this->parseCookies();
        }
        return $this->cookies[$name] ?? $default;
    }

    /**
     * Parse cookies from the `cookie` header
     *
     * @return array<string,mixed>
     */
    protected function parseCookies(): array
    {
        # cookies are encoded in the `cookie` header
        $header = $this->getHeader("cookie");
        if (empty($header)) {
            return [];
        }

        # create a data collection to gracefully handle arrays
        $options = $this->getDecoderOptions();
        $collection = new \sndsgd\http\data\Collection(
            $options->getMaxVars(),
            $options->getMaxNestingLevels()
        );

        foreach (explode(";", $header) as $cookie) {
            $cookie = trim($cookie);
            if ($cookie === "") {
                continue;
            }

            $pair = explode("=", $cookie, 2);
            if (!isset($pair[1]) || $pair[1] === "") {
                continue;
            }

            list($key, $value) = $pair;
            $collection->addValue(urldecode($key), urldecode($value));
        }

        return $collection->getValues();
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
                    $this->getContentLength(),
                    $this->getDecoderOptions()
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
