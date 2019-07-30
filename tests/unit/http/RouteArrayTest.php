<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\RouteArray
 */
class RouteArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException \BadMethodCallException
     */
    public function testConstructorException()
    {
        new RouteArray();
    }

    /**
     * @dataProvider provideConstructor
     */
    public function testConstructor(array $routes)
    {
        $this->assertCount(count($routes), new RouteArray(...$routes));
    }

    public function provideConstructor()
    {
        return [
            [[new Route("GET", "/v1")]],
            [[new Route("GET", "/v1"), new Route("GET", "/v2")]],
        ];
    }
}
