<?php

namespace sndsgd\http;


/**
 * @coversDefaultClass \sndsgd\http\Code
 */
class CodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getStatusText
     */
    public function testGetStatusText()
    {
        $this->assertEquals("OK", Code::getStatusText(200));
        $this->assertEquals("Created", Code::getStatusText(201));

        $this->assertEquals("Moved Permanently", Code::getStatusText(301));
        $this->assertEquals("Found", Code::getStatusText(302));

        $this->assertEquals("Bad Request", Code::getStatusText(400));
        $this->assertEquals("Not Found", Code::getStatusText(404));

        $this->assertEquals("Internal Server Error", Code::getStatusText(500));
        $this->assertEquals("Bandwidth Limit Exceeded", Code::getStatusText(509));

        $this->assertNull(Code::getStatusText(600));
        $this->assertNull(Code::getStatusText(0));
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertTrue(Code::matches(200, "2xx"));
        $this->assertTrue(Code::matches(200, 200));
        $this->assertFalse(Code::matches(200, 201));
        $this->assertFalse(Code::matches(200, "21x"));
        $this->assertFalse(Code::matches(404, "2xx"));
    }

    /**
     * @covers ::matches
     * @expectedException InvalidArgumentException
     */
    public function testMatchesIntegerException()
    {
        Code::matches(200, "abs");
    }
}
