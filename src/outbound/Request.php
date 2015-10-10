<?php

namespace sndsgd\http\outbound;

use \sndsgd\http\HeaderTrait;
use \sndsgd\http\MethodTrait;


/**
 * Base class for outbound requests
 */
abstract class Request
{
    use HeaderTrait, MethodTrait;

    /**
     * The request url
     *
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Send the request and return an initialized response
     *
     * @param string $class A response classname as string
     * @return \sndsgd\http\inbound\Response
     */
    abstract public function getResponse($class = null);
}