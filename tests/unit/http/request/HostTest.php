<?php

namespace sndsgd\http\request;

/**
 * @coversDefaultClass \sndsgd\http\request\Host
 */
class HostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $request = createTestRequest();
        $client = new Host($request);
    }

    /**
     * @covers ::getIp
     * @dataProvider providerGetIp
     */
    public function testGetIp(array $server, $expect)
    {
        $host = createTestHost($server);
        $this->assertSame($expect, $host->getIp());
    }

    public function providerGetIp()
    {
        return [
            [[], ""],
            [["SERVER_ADDR" => "111.222.111.222"], "111.222.111.222"],
        ];
    }

    /**
     * @covers ::getDnsName
     * @dataProvider providerGetDnsName
     */
    public function testGetDnsName(array $server, $expect)
    {
        $host = createTestHost($server);
        $this->assertSame($expect, $host->getDnsName());
    }

    public function providerGetDnsName()
    {
        return [
            [[], ""],
            [["SERVER_NAME" => "localhost"], "localhost"],
            [["SERVER_NAME" => "test.test"], "test.test"],
        ];
    }
}
