<?php

namespace sndsgd\http\inbound;


# used to test `Request::getBodyParameters()`
class ExtendedRequest extends Request
{
    protected static $dataTypes = [
        "test/test" => "sndsgd\\http\\inbound\\FakeDataDecoder",
    ];
}

# used to test `Request::getBodyParameters()`
class FakeDataDecoder extends \sndsgd\http\data\Decoder
{
    public function getDecodedData()
    {
        return ["one" => "1", "two" => "two"];
    }
}


/**
 * @coversDefaultClass \sndsgd\http\inbound\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    private static $server;

    public function setUp()
    {
        static::$server = $_SERVER;
    }

    public function tearDown()
    {
        $_SERVER = static::$server;
    }

    private function initServer(array $server = [])
    {
        if (!array_key_exists("REQUEST_METHOD", $server)) {
            $server["REQUEST_METHOD"] = "GET";
        }
        if (!array_key_exists("REQUEST_URI", $server)) {
            $server["REQUEST_URI"] = "/some/path";
        }
        return $server;
    }

    /**
     * @covers ::__construct
     * @covers ::getMethod
     * @covers ::getPath
     */
    public function testConstructor()
    {
        $_SERVER = [
            "REQUEST_METHOD" => "GET",
            "REQUEST_URI" => "/some/path?query=value"
        ];
        $req = new Request();
        $this->assertEquals("GET", $req->getMethod());
        $this->assertEquals("/some/path", $req->getPath());

        $_SERVER = [
            "REQUEST_METHOD" => "POST",
            "REQUEST_URI" => "/1/2/3/4/5/?query=value#important"
        ];
        $req = new Request();
        $this->assertEquals("POST", $req->getMethod());
        $this->assertEquals("/1/2/3/4/5/", $req->getPath());
    }

    /**
     * @covers ::getHeader
     */
    public function testGetHeader()
    {
        $_SERVER = $this->initServer([
            "HTTP_X_APP_TEST" => "test",
        ]);

        $req = new Request();
        $this->assertEquals("test", $req->getHeader("x-app-test"));
        $this->assertEquals("test", $req->getHeader("X-App-Test"));
        $this->assertEquals("test", $req->getHeader("X-APP-TEST"));
    }

    /**
     * @covers ::getContentType
     * @dataProvider contentTypeProvider
     */
    public function testGetContentType($input, $expect)
    {
        $_SERVER = $this->initServer(["HTTP_CONTENT_TYPE" => $input]);
        $req = new Request();
        $this->assertEquals($expect, $req->getContentType());
    }

    public function contentTypeProvider()
    {
        return [
            ["application/json", "application/json"],
            ["application/json; charset=UTF-8", "application/json"],
            ["TEXT/html", "text/html"],
        ];
    }

    /**
     * @covers ::getAcceptContentType
     * @dataProvider acceptContentTypeProvider
     */
    public function testGetAcceptContentType($input, $expect)
    {
        $_SERVER = $this->initServer(["HTTP_ACCEPT" => $input]);
        $req = new Request();
        $this->assertEquals($expect, $req->getAcceptContentType());
    }

    public function acceptContentTypeProvider()
    {
        return [
            ["application/json,image/webp,*/*;q=0.8", "application/json"],
            ["application/xml,*/*;asd=1.0", "application/xml"],
            ["TEXT/html", "text/html"],
            ["", ""],
        ];
    }

    /**
     * @covers ::getBasicAuth
     * @dataProvider basicAuthProvider
     */
    public function testGetBasicAuth($server, $expect)
    {
        $_SERVER = $this->initServer($server);
        $req = new Request();
        $this->assertEquals($expect, $req->getBasicAuth());
    }

    public function basicAuthProvider()
    {
        return [
            [
                [],
                [null, null]
            ],
            [
                [
                    "PHP_AUTH_USER" => "user"
                ],
                ["user", null]
            ],
            [
                [
                    "PHP_AUTH_PW" => "password"
                ],
                [null, "password"]
            ],
            [
                [
                    "PHP_AUTH_USER" => "user",
                    "PHP_AUTH_PW" => "password"
                ],
                ["user", "password"]
            ]
        ];
    }

    /**
     * @covers ::setUriParameters
     * @covers ::getUriParameters
     */
    public function testSetGetUriParameters()
    {
        $params = ["one" => "1", "two" => "two"];
        $_SERVER = $this->initServer();
        $req = new Request();
        $req->setUriParameters($params);
        $this->assertEquals($params, $req->getUriParameters());
    }

    /**
     * @covers ::getQueryParameters
     */
    public function testGetQueryParameters()
    {
        $_SERVER = $this->initServer(["REQUEST_URI" => "/?one=1&two=two"]);
        $expect = ["one" => 1, "two" => "two"];
        $req = new Request();
        $result = $req->getQueryParameters();
        $this->assertEquals($expect, $result);
    }

    /**
     * @covers ::getBodyParameters
     */
    public function testGetBodyParameters()
    {
        $_SERVER = $this->initServer(["HTTP_CONTENT_TYPE" => "test/test"]);
        $req = new ExtendedRequest();
        $params = $req->getBodyParameters();
        $this->assertEquals(["one" => "1", "two" => "two"], $params);
    }

    /**
     * @covers ::getBodyParameters
     * @expectedException Exception
     */
    public function testGetBodyParametersNoContentType()
    {
        # no content type is provided
        $_SERVER = $this->initServer();
        $req = new ExtendedRequest();
        $params = $req->getBodyParameters();
    }

    /**
     * @covers ::getBodyParameters
     * @expectedException Exception
     */
    public function testGetBodyParametersUnknownContentType()
    {
        $_SERVER = $this->initServer(["HTTP_CONTENT_TYPE" => "no/decoder"]);
        $req = new ExtendedRequest();
        $params = $req->getBodyParameters();
    }

    /**
     * @covers ::getRawBody
     */
    public function testGetRawBody()
    {
        $_SERVER = $this->initServer();
        $req = new ExtendedRequest();
        $this->assertEquals("", $req->getRawBody());
    }
}
