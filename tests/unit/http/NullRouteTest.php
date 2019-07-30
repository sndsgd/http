<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\NullRoute
 */
class NullRouteTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $route = new NullRoute();
        $this->assertSame("", $route->getMethod());
        $this->assertSame("", $route->getPath());
        $this->assertSame(0, $route->getPriority());
    }
}
