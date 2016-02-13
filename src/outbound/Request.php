<?php

namespace sndsgd\http\outbound;

use \sndsgd\http\MethodTrait;


/**
 * Base class for outbound requests
 */
abstract class Request
{
    use MethodTrait;

    /**
     * The request url
     *
     * @var string
     */
    protected $url;

    /**
     * Request headers
     *
     * @var \sndsgd\http\HeaderCollection
     */
    protected $headers;


    public function __construct()
    {
        $this->headers = new \sndsgd\http\HeaderCollection();
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setHeader(string $key, string $value): Request
    {
        $this->headers->setHeader($key, $value);
        return $this;
    }

    public function setHeaders(array $headers): Request
    {
        $this->headers->setMultiple($headers);
        return $this;
    }

    public function addHeader(string $key, string $value): Request
    {
        $this->headers->addHeader($key, $value);
        return $this;
    }

    public function addHeaders(array $headers): Request
    {
        $this->headers->addMultiple($headers);
        return $this;
    }

    /**
     * Send the request and return an initialized response
     *
     * @param string $class A response classname as string
     * @return \sndsgd\http\inbound\Response
     */
    abstract public function getResponse($class = null);
}
