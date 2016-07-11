<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\Route
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__toString
     * @dataProvider providerToString
     */
    public function testToString($method, $path, $expect)
    {
        $route = new Route($method, $path);
        $this->assertSame($expect, (string) $route);
        $this->assertSame($expect, $route->__toString());
    }

    public function providerToString()
    {
        return [
            ["post", "/", "POST:/"],
            ["GeT", "/some/path", "GET:/some/path"],
        ];
    }

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
            ["post", "/", 1, "POST"],
            ["POST", "/", 1, "POST"],
            ["PATCH", "/", 1, "PATCH"],
            ["PUT", "/", 1, "PUT"],
            ["DELETE", "/", 1, "DELETE"],
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
            ["GET", "/test/💩/💩", 1, "/test/💩/💩"],
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
