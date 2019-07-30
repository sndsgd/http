<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\Scheme
 */
class SchemeTest extends \PHPUnit\Framework\TestCase
{
    public function testConstants()
    {
        $this->assertSame(Scheme::HTTP, "http");
        $this->assertSame(Scheme::HTTPS, "https");
    }
}
