<?php

namespace sndsgd\http;

class Response
{
    /**
     * The http status code
     *
     * @var integer
     */
    protected $statusCode = 200;

    /**
     * The http status text
     *
     * @var string
     */
    protected $statusText = "OK";

    /**
     * @var \sndsgd\http\HeaderCollection
     */
    protected $headers;

    /**
     * Once the body has been generated it is stored here
     *
     * @var string
     */
    protected $body;

    /**
     * @param \sndsgd\http\Request $request
     */
    public function __construct(
        int $statusCode = \sndsgd\http\Status::OK,
        \sndsgd\http\HeaderCollection $headers = null,
        string $body = ""
    )
    {
        $this->setStatus($statusCode);
        $this->headers = $headers ?? new \sndsgd\http\HeaderCollection();
        $this->setBody($body);
    }

    /**
     * Set the status code and text
     *
     * @see \sndsgd\http\Code
     * @param integer $code The http status code
     * @return \sndsgd\http\Response
     */
    public function setStatus(int $code): Response
    {
        # `Status::getText` will throw an exception for invalid status codes
        $this->statusText = Status::getText($code);
        $this->statusCode = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        return $this->statusText;
    }

    /**
     * @param string $key
     * @param string $value
     * @return \sndsgd\http\Response
     */
    public function setHeader(string $key, string $value): Response
    {
        $this->headers->set($key, $value);
        return $this;
    }

    /**
     * Add a header
     * Allows for adding multiple headers with the same key
     *
     * @param string $key
     * @param string $value
     * @return \sndsgd\http\Response
     */
    public function addHeader(string $key, string $value): Response
    {
        $this->headers->add($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @return \sndsgd\http\Response
     */
    public function getHeader(string $key): string
    {
        return $this->headers->get($key);
    }

    /**
     * @param array $headers
     * @return \sndsgd\http\Response
     */
    public function setHeaders(array $headers): Response
    {
        $this->headers->setMultiple($headers);
        return $this;
    }

    /**
     * @param string $body
     * @return \sndsgd\http\Response
     */
    public function setBody(string $body): Response
    {
        $contentLength = strlen($body);
        if ($contentLength > 0) {
            $this->setHeader("Content-Length", $contentLength);
        }

        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Send the request
     *
     * @param string $protocol The request protocol
     * @return void
     */
    public function send(string $protocol)
    {
        header("$protocol {$this->statusCode} {$this->statusText}");
        foreach ($this->headers->getStringifiedArray() as $header) {
            header($header);
        }

        echo $this->body;
    }
}
