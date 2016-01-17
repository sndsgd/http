<?php

namespace sndsgd\http;


/**
 * @coversDefaultClass \sndsgd\http\Host
 */
class HostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::setUrl
     */
    public function testSetUrl()
    {
        $host = new Host;
        $host->setUrl("https://a.com");
        $this->assertInstanceOf("sndsgd\\Url", $host->getUrl());
    }

    /**
     * @covers ::setUrl
     * @expectedException InvalidArgumentException
     */
    public function testSetUrlException()
    {
        $host = new Host;
        $host->setUrl("/no/scheme/no/hostname");
    }

    /**
     * @covers ::getUrl
     */
    public function testGetUrl()
    {
        $host = new Host;
        $host->setUrl("http://a.com");
        $this->assertEquals("http://a.com/", $host->getUrl(null, true));
        $url = $host->getUrl("/some/path", true);
        $this->assertEquals("http://a.com/some/path", $url);
        $url = $host->getUrl("some/path", true);
        $this->assertEquals("http://a.com/some/path", $url);
    }

    /**
     * @covers ::setOptions
     * @covers ::getOptions
     */
    public function testSetGetOptions()
    {
        $test = [1 => "one", "two" => "two"];
        $host = new Host;
        $host->setOptions($test);
        $this->assertEquals($test, $host->getOptions());
    }
}
