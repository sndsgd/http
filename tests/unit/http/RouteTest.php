<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\Route
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerGetMethod
     */
    public function testGetMethod($method, $path, $priority, $expect)
    {
        $route = new Route($method, $path, $priority);
        $this->assertSame($expect, $route->getMethod());
    }

    public function providerGetMethod()
    {
        return [
            ["GET", "/", 1, "GET"],
            ["get", "/", 1, "GET"],
        ];
    }

    /**
     * @dataProvider providerGetPath
     */
    public function testGetPath($method, $path, $priority, $expect)
    {
        $route = new Route($method, $path, $priority);
        $this->assertSame($expect, $route->getPath());
    }

    public function providerGetPath()
    {
        return [
            ["GET", "/", 1, "/"],
            ["GET", "/test/path", 1, "/test/path"],
        ];
    }

    /**
     * @dataProvider providerGetPriority
     */
    public function testGetPriority($method, $path, $priority, $expect)
    {
        $route = new Route($method, $path, $priority);
        $this->assertSame($expect, $route->getPriority());
    }

    public function providerGetPriority()
    {
        return [
            ["GET", "/", 1, 1],
            ["GET", "/", 42, 42],
        ];
    }
}
