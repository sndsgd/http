<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @dataProvider providerConstructor
     */
    public function testConstructor(array $server)
    {
        $req = new Request($server);
        $rc = new \ReflectionClass($req);
        $property = $rc->getProperty("server");
        $property->setAccessible(true);
        $this->assertSame($server, $property->getValue($req));
    }

    public function providerConstructor()
    {
        return [
            [
                [
                    "key" => \sndsgd\Str::random(100),
                ],
            ],
            [
                [
                    "REQUEST_METHOD" => "GET",
                    "REQUEST_URI" => "/some/path?query=value"
                ],
            ],
            [
                [
                    "REQUEST_METHOD" => "POST",
                    "REQUEST_URI" => "/1/a/2/b/3/c/?query=value"
                ],
            ],
        ];
    }

    /**
     * @covers ::getMethod
     * @dataProvider providerGetMethod
     */
    public function testGetMethod(array $server, $expect)
    {
        $req = new Request($server);
        $this->assertSame($expect, $req->getMethod());
        $this->assertSame($expect, $req->getMethod());
    }

    public function providerGetMethod()
    {
        $methods = [
            "GET",
            "POST",
            "PATCH",
            "PUT",
            "DELETE",
            "HEAD",
            "OPTIONS",
            \sndsgd\Str::random(32),
            \sndsgd\Str::random(32),
        ];

        $ret = [[[], "GET"]];
        foreach ($methods as $method) {
            $ret[] = [["REQUEST_METHOD" => $method], $method];
        }
        return $ret;
    }

    /**
     * @covers ::getPath
     * @dataProvider providerGetPath
     */
    public function testGetPath(array $server, $expect)
    {
        $req = new Request($server);
        $this->assertSame($expect, $req->getPath());
        $this->assertSame($expect, $req->getPath());
    }

    public function providerGetPath()
    {
        return [
            [[], "/"],
            [["REQUEST_URI" => "/"], "/"],
            [["REQUEST_URI" => "/a/b/c?a=1&b=2&c=3"], "/a/b/c"],
            [["REQUEST_URI" => "/a/b/c/?a=1&b=2&c=3"], "/a/b/c/"],
            [["REQUEST_URI" => "/test/@/:?a=1&b=2&c=3"], "/test/@/:"],
        ];
    }

    /**
     * @covers ::getProtocol
     * @dataProvider providerGetProtocol
     */
    public function testGetProtocol($server, $expect)
    {
        $req = new Request($server);
        $this->assertSame($expect, $req->getProtocol());
    }

    public function providerGetProtocol()
    {
        return [
            [[], "HTTP/1.1"],
            [["SERVER_PROTOCOL" => "asd"], "asd"],
        ];
    }

    /**
     * @covers ::getHost
     * @dataProvider providerGetHost
     */
    public function testGetHost(array $server, $expect)
    {
        $req = new Request($server);
        $this->assertSame($expect, $req->getHost());
    }

    public function providerGetHost()
    {
        return [
            [[], ""],
            [["HTTP_HOST" => "asd"], "asd"],
        ];
    }

    /**
     * @covers ::getIp
     * @dataProvider providerGetIp
     */
    public function testGetIp(array $server, $expect)
    {
        $req = new Request($server);
        $this->assertSame($expect, $req->getIp());
    }

    public function providerGetIp()
    {
        return [
            [[], ""],
            [["HTTP_X_FORWARDED_FOR" => "123"], "123"],
            [["X_FORWARDED_FOR" => "456"], "456"],
            [["REMOTE_ADDR" => "789"], "789"],
        ];
    }

    /**
     * @covers ::getAcceptContentTypes
     * @dataProvider acceptContentTypeProviders
     */
    public function testGetAcceptContentTypes($header, $expect)
    {
        $req = new Request(["HTTP_ACCEPT" => $header]);
        $this->assertEquals($expect, $req->getAcceptContentTypes());
    }

    public function acceptContentTypeProviders()
    {
        return [
            [
                "application/json,image/webp,*/*;q=0.8",
                [
                    "application/json" => "application/json",
                    "image/webp" => "image/webp",
                    "*/*" => "*/*",
                ],
            ],
            [
                "application/json,image/webp,*/*;q=0.8",
                [
                    "application/json" => "application/json",
                    "image/webp" => "image/webp",
                    "*/*" => "*/*",
                ],
            ],
            [
                "application/xml,*/*;asd=1.0",
                [
                    "application/xml" => "application/xml",
                    "*/*" => "*/*",
                ],
            ],
            [
                "TEXT/html",
                [
                    "text/html" => "text/html",
                ],
            ],
            [
                "",
                [],
            ],
        ];
    }

    /**
     * @dataProvider providerGetHeader
     */
    public function testGetHeader($server, $header, $default, $expect)
    {
        $request = new Request($server);
        $this->assertSame($expect, $request->getHeader($header, $default));
    }

    public function providerGetHeader()
    {
        return [
            [
                ["HTTP_SOME_VALUE" => "test"],
                "some-value",
                "",
                "test",
            ],
            [
                ["HTTP_SOME_VALUE" => "test"],
                "SOME-VALUE",
                "",
                "test",
            ],
            [
                ["HTTP_SOME_VALUE" => "test"],
                "a-value",
                "default",
                "default",
            ],
        ];
    }

    /**
     * @dataProvider providerGetHeaders
     */
    public function testGetHeaders($server, $expect)
    {
        $request = new Request($server);
        $this->assertSame($expect, $request->getHeaders());
    }

    public function providerGetHeaders()
    {
        return [
            [
                ["HTTP_ONE" => "1", "HTTP_OTHER_VALUE" => "asd"],
                ["one" => "1", "other-value" => "asd"],
            ],
        ];
    }

    /**
     * @dataProvider providerGetContentType
     */
    public function testGetContentType($server, $expect)
    {
        $request = new Request($server);
        $this->assertSame($expect, $request->getContentType());
    }

    public function providerGetContentType()
    {
        return [
            [
                ["HTTP_CONTENT_TYPE" => "application/json"],
                "application/json",
            ],
            [
                ["HTTP_CONTENT_TYPE" => "application/json; charset=UTF-8"],
                "application/json",
            ],
            [
                ["HTTP_CONTENT_TYPE" => "TEXT/html"],
                "text/html",
            ],
        ];
    }

    /**
     * @dataProvider providerGetContentLength
     */
    public function testGetContentLength($server, $expect)
    {
        $request = new Request($server);
        $this->assertSame($expect, $request->getContentLength());
    }

    public function providerGetContentLength()
    {
        return [
            [[], 0],
            [["HTTP_CONTENT_LENGTH" => "42"], 42],
        ];
    }

    /**
     * @dataProvider providerGetBasicAuth
     */
    public function testGetBasicAuth($server, $expect)
    {
        $request = new Request($server);
        $this->assertSame($expect, $request->getBasicAuth());
    }

    public function providerGetBasicAuth()
    {
        return [
            [
                [],
                ["", ""],
            ],
            [
                ["PHP_AUTH_USER" => "user", "PHP_AUTH_PW" => "pass"],
                ["user", "pass"],
            ]
        ];
    }

    /**
     * @covers ::getQueryParameters
     * @dataProvider providerGetQueryParameters
     */
    public function testGetQueryParameters($server, $expect)
    {
        $request = new Request($server);
        $this->assertSame($expect, $request->getQueryParameters());
    }

    public function providerGetQueryParameters()
    {
        return [
            [
                [],
                [],
            ],
            [
                ["QUERY_STRING" => "one=1&two=two"],
                ["one" => "1", "two" => "two"],
            ],
        ];
    }

    /**
     * @covers ::getBodyDecoder
     */
    public function testGetBodyDecoder()
    {
        $request = new Request([]);
        $rc = new \ReflectionClass($request);
        $method = $rc->getMethod('getBodyDecoder');
        $method->setAccessible(true);
        $bodyDecoder = $method->invoke($request);
        $this->assertInstanceOf(request\BodyDecoder::class, $bodyDecoder);
    }

    /**
     * @covers ::getBodyParameters
     * @dataProvider providerGetBodyParameters
     */
    public function testGetBodyParameters($server, $expect)
    {
        $bodyDecoder = $this->getMockBuilder(request\BodyDecoder::class)
            ->setMethods(['decode'])
            ->getMock();

        $bodyDecoder->method('decode')->willReturn($expect);

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$server])
            ->setMethods(['getBodyDecoder'])
            ->getMock();

        $request->method('getBodyDecoder')->willReturn($bodyDecoder);

        $this->assertSame($expect, $request->getBodyParameters());
    }

    public function providerGetBodyParameters()
    {
        return [
            [
                [],
                []
            ],
            [
                ["HTTP_CONTENT_TYPE" => "application/json"],
                []
            ],
        ];
    }
}
