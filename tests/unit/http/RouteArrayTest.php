<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\RouteArray
 */
class RouteArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \BadMethodCallException
     */
    public function testConstructorException()
    {
        new RouteArray();
    }

    public function testConstructor()
    {
        new RouteArray(new Route("GET", "/v1"));
        new RouteArray(new Route("GET", "/v1"), new Route("GET", "/v2"));
    }
}
