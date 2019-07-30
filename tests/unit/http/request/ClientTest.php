<?php

namespace sndsgd\http\request;

/**
 * @coversDefaultClass \sndsgd\http\request\Client
 */
class ClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $client = new Client(createTestRequest());
        # assert instanceof to prevent "test did not perform any assertions"
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @covers ::getIp
     * @dataProvider providerGetIp
     */
    public function testGetIp(array $server, $expect)
    {
        $client = createTestClient($server);
        $this->assertSame($expect, $client->getIp());
        $this->assertSame($expect, $client->getIp());
    }

    public function providerGetIp()
    {
        return [
            [[], ""],
            [["REMOTE_ADDR" => "789"], "789"],
            [["HTTP_X_FORWARDED_FOR" => "123"], "123"],
            [["HTTP_X_FORWARDED_FOR" => "first,second,third"], "first"],
            [["HTTP_X_FORWARDED_FOR" => "abc, def, ghi"], "abc"],
            [["X_FORWARDED_FOR" => "456"], "456"],
            [["X_FORWARDED_FOR" => "first,second,third"], "first"],
            [["X_FORWARDED_FOR" => "abc, def, ghi"], "abc"],
        ];
    }
}
