<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\Scheme
 */
class SchemeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstants()
    {
        $this->assertSame(Scheme::HTTP, "http");
        $this->assertSame(Scheme::HTTPS, "https");
    }
}
