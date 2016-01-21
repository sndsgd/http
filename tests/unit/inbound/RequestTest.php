<?php

namespace sndsgd\http\inbound;

/**
 * @coversDefaultClass \sndsgd\http\inbound\Request
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
     * @covers ::getHost
     * @dataProvider providerGetHost
     */
    public function testGetHost(array $server, $expect)
    {
        $req = new Request($server);
        $this->assertSame($expect, $req->getHost());
        $this->assertSame($expect, $req->getHost());
    }

    public function providerGetHost()
    {
        return [
            [[], ""],
            [["HTTP_HOST" => "asd"], "asd"],
        ];
    }



    // /**
    //  * @covers ::getHeader
    //  */
    // public function testGetHeader()
    // {
    //     $_SERVER = $this->initServer([
    //         "HTTP_X_APP_TEST" => "test",
    //     ]);

    //     $req = new Request();
    //     $this->assertEquals("test", $req->getHeader("x-app-test"));
    //     $this->assertEquals("test", $req->getHeader("X-App-Test"));
    //     $this->assertEquals("test", $req->getHeader("X-APP-TEST"));
    // }

    // /**
    //  * @covers ::getContentType
    //  * @dataProvider contentTypeProvider
    //  */
    // public function testGetContentType($input, $expect)
    // {
    //     $_SERVER = $this->initServer(["HTTP_CONTENT_TYPE" => $input]);
    //     $req = new Request();
    //     $this->assertEquals($expect, $req->getContentType());
    // }

    // public function contentTypeProvider()
    // {
    //     return [
    //         ["application/json", "application/json"],
    //         ["application/json; charset=UTF-8", "application/json"],
    //         ["TEXT/html", "text/html"],
    //     ];
    // }

    /**
     * @covers ::getAcceptContentType
     * @dataProvider acceptContentTypeProvider
     */
    public function testGetAcceptContentType($header, $all, $expect)
    {
        $req = new Request(["HTTP_ACCEPT" => $header]);
        $this->assertEquals($expect, $req->getAcceptContentType($all));
    }

    public function acceptContentTypeProvider()
    {
        return [
            [
                "application/json,image/webp,*/*;q=0.8",
                false,
                "application/json",
            ],
            [
                "application/json,image/webp,*/*;q=0.8",
                true,
                [
                    "application/json" => true,
                    "image/webp" => true,
                    "*/*" => true,
                ],
            ],
            [
                "application/xml,*/*;asd=1.0",
                false,
                "application/xml",
            ],
            [
                "TEXT/html",
                false,
                "text/html",
            ],
            [
                "",
                false,
                "",
            ],
        ];
    }

    // /**
    //  * @covers ::getBasicAuth
    //  * @dataProvider basicAuthProvider
    //  */
    // public function testGetBasicAuth($server, $expect)
    // {
    //     $_SERVER = $this->initServer($server);
    //     $req = new Request();
    //     $this->assertEquals($expect, $req->getBasicAuth());
    // }

    // public function basicAuthProvider()
    // {
    //     return [
    //         [
    //             [],
    //             [null, null]
    //         ],
    //         [
    //             [
    //                 "PHP_AUTH_USER" => "user"
    //             ],
    //             ["user", null]
    //         ],
    //         [
    //             [
    //                 "PHP_AUTH_PW" => "password"
    //             ],
    //             [null, "password"]
    //         ],
    //         [
    //             [
    //                 "PHP_AUTH_USER" => "user",
    //                 "PHP_AUTH_PW" => "password"
    //             ],
    //             ["user", "password"]
    //         ]
    //     ];
    // }

    // /**
    //  * @covers ::setUriParameters
    //  * @covers ::getUriParameters
    //  */
    // public function testSetGetUriParameters()
    // {
    //     $params = ["one" => "1", "two" => "two"];
    //     $_SERVER = $this->initServer();
    //     $req = new Request();
    //     $req->setUriParameters($params);
    //     $this->assertEquals($params, $req->getUriParameters());
    // }

    // /**
    //  * @covers ::getQueryParameters
    //  */
    // public function testGetQueryParameters()
    // {
    //     $_SERVER = $this->initServer(["REQUEST_URI" => "/?one=1&two=two"]);
    //     $expect = ["one" => 1, "two" => "two"];
    //     $req = new Request();
    //     $result = $req->getQueryParameters();
    //     $this->assertEquals($expect, $result);
    // }

    // /**
    //  * @covers ::getBodyParameters
    //  */
    // public function testGetBodyParameters()
    // {
    //     $_SERVER = $this->initServer(["HTTP_CONTENT_TYPE" => "test/test"]);
    //     $req = new ExtendedRequest();
    //     $params = $req->getBodyParameters();
    //     $this->assertEquals(["one" => "1", "two" => "two"], $params);
    // }

    // /**
    //  * @covers ::getBodyParameters
    //  * @expectedException Exception
    //  */
    // public function testGetBodyParametersNoContentType()
    // {
    //     # no content type is provided
    //     $_SERVER = $this->initServer();
    //     $req = new ExtendedRequest();
    //     $params = $req->getBodyParameters();
    // }

    // /**
    //  * @covers ::getBodyParameters
    //  * @expectedException Exception
    //  */
    // public function testGetBodyParametersUnknownContentType()
    // {
    //     $_SERVER = $this->initServer(["HTTP_CONTENT_TYPE" => "no/decoder"]);
    //     $req = new ExtendedRequest();
    //     $params = $req->getBodyParameters();
    // }

    // /**
    //  * @covers ::getRawBody
    //  */
    // public function testGetRawBody()
    // {
    //     $_SERVER = $this->initServer();
    //     $req = new ExtendedRequest();
    //     $this->assertEquals("", $req->getRawBody());
    // }
}
