<?php

namespace sndsgd\http;

interface RequestInterface extends RequestParameterDecoderInterface
{
    /**
     * Retreive the envrionment instance
     *
     * @return \sndsgd\Environment
     */
    public function getEnvironment(): \sndsgd\Environment;

    /**
     * Retreive the request host instance
     *
     * @return \sndsgd\http\request\HostInterface
     */
    public function getHost(): \sndsgd\http\request\HostInterface;

    /**
     * Retreive the request client instance
     *
     * @return \sndsgd\http\request\ClientInterface
     */
    public function getClient(): \sndsgd\http\request\ClientInterface;

    /**
     * Retrieve the http method
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Retrieve the uri path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get the request protocol
     *
     * @return string
     */
    public function getProtocol(): string;

    /**
     * Get the request scheme
     *
     * @return string Either 'https' or 'http'
     */
    public function getScheme(): string;

    /**
     * Determine whether the request was made over an https connection
     *
     * @return bool
     */
    public function isHttps(): bool;

    /**
     * Retrieve a header
     *
     * @param string $name The name of the header to get
     * @param string $default A value to use if the header does not exist
     * @return string
     */
    public function getHeader(string $name, string $default = ""): string;

    /**
     * Retrieve all headers
     *
     * @return array<string,string>
     */
    public function getHeaders(): array;

    /**
     * Retrieve the content type
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Retrieve the body content length
     *
     * @return int
     */
    public function getContentLength(): int;

    /**
     * Get client specified acceptable content types
     *
     * @return array<string,string>
     */
    public function getAcceptContentTypes(): array;

    /**
     * Get the basic auth credentials
     *
     * @return array<string>
     */
    public function getBasicAuth(): array;

    /**
     * Retrieve all cookies
     *
     * @return array<string,mixed>
     */
    public function getCookies(): array;

    /**
     * Retrieve a single cookie
     *
     * @param string $name The cookie to retrieve
     * @param mixed $default The value to return if the cookie does not exist
     * @return mixed
     */
    public function getCookie(string $name, $default = "");

    /**
     * {@inheritdoc}
     *
     * @see \sndsgd\http\RequestParameterDecoderInterface
     */
    public function getQueryParameters(): array;

    /**
     * {@inheritdoc}
     *
     * @see \sndsgd\http\RequestParameterDecoderInterface
     */
    public function getBodyParameters(): array;
}
