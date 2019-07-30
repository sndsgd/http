<?php

namespace sndsgd\http\request;

use \org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \sndsgd\http\request\BodyDecoder
 */
class BodyDecoderTest extends \PHPUnit\Framework\TestCase
{
    const PARSE_POST = ["post-was-parsed"];
    const DECODE = ["body-was-decoded"];

    public function setup()
    {
        $this->decoders = $this->getDecodersProperty()->getValue();
    }

    public function tearDown()
    {
        $_POST = [];
        $_FILES = [];

        $this->getDecodersProperty()->setValue($this->decoders);
    }

    private function getDecodersProperty()
    {
        $rc = new \ReflectionClass(BodyDecoder::class);
        $property = $rc->getProperty("decoders");
        $property->setAccessible(true);
        return $property;
    }

    public function testAddDecoder()
    {
        $startCount = count($this->decoders);

        # adding an additional json content type
        $class = \sndsgd\http\data\decoder\JsonDecoder::class;
        BodyDecoder::addDecoder($class, "application/json", "text/json");
        $decoders = $this->getDecodersProperty()->getValue();
        $this->assertSame($startCount + 1, count($decoders));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddDecoderException()
    {
        BodyDecoder::addDecoder(stdClass::class, "wonky/wonky");
    }

    /**
     * @dataProvider providerDecode
     */
    public function testDecode($options, $expect)
    {
        $mock = $this->getMockBuilder(BodyDecoder::class)
            ->setMethods(["parsePost", "getDecoder"])
            ->getMock();

        $mock->method("parsePost")->willReturn(self::PARSE_POST);
        $mock->method("getDecoder")->willReturn($this->createDecoderMock());

        $result = $mock->decode("POST", "", "not/json", 42, $options);
        $this->assertsame($expect, $result);
    }

    public function providerDecode()
    {
        return [
            [$this->createOptions(true), self::PARSE_POST],
            [$this->createOptions(false), self::DECODE],
        ];
    }

    private function createDecoderMock()
    {
        $mock = $this->getMockBuilder(\sndsgd\http\data\decoder\JsonDecoder::class)
            ->disableOriginalConstructor()
            ->setMethods(["decode"])
            ->getMock();

        $mock->method("decode")->willReturn(self::DECODE);
        return $mock;
    }

    private function createOptions($postDataReadingEnabled)
    {
        $options = $this->getMockBuilder(\sndsgd\http\data\decoder\DecoderOptions::class)
            ->setMethods(["getPostDataReadingEnabled"])
            ->getMock();

        $options->method("getPostDataReadingEnabled")->willReturn($postDataReadingEnabled);
        return $options;
    }

    /**
     * @dataProvider providerGetDecoder
     */
    public function testGetDecoder($contentType, $exception = "")
    {
        if ($exception) {
            $this->expectException($exception);
        }

        $options = new \sndsgd\http\data\decoder\DecoderOptions();

        $bodyDecoder = new BodyDecoder();
        $rc = new \ReflectionClass($bodyDecoder);
        $method = $rc->getMethod("getDecoder");
        $method->setAccessible(true);
        $result = $method->invoke($bodyDecoder, "", $contentType, 42, $options);
        $expect = "sndsgd\\http\\data\\decoder\\DecoderInterface";

        $this->assertInstanceOf($expect, $result);
    }

    public function providerGetDecoder()
    {
        return [
            ["application/json", ""],
            ["unsupported/type", \Exception::class],
        ];
    }

    /**
     * @dataProvider providerParsePost
     */
    public function testParsePost($post, $expect)
    {
        $_POST = $post;
        $_FILES = [];

        $bodyDecoder = new BodyDecoder();
        $rc = new \ReflectionClass($bodyDecoder);
        $method = $rc->getMethod("parsePost");
        $method->setAccessible(true);
        $this->assertEquals($expect, $method->invoke($bodyDecoder));
    }

    public function providerParsePost()
    {
        return [
            [
                ["test" => "value"],
                ["test" => "value"],
            ],
        ];
    }

    /**
     * @dataProvider providerParsePostFiles
     */
    public function testParsePostFiles($files, $expectCount)
    {
        $_POST = [];
        $_FILES = $files;

        $bodyDecoder = new BodyDecoder();
        $method = new \ReflectionMethod($bodyDecoder, "parsePost");
        $method->setAccessible(true);
        $result = $method->invoke($bodyDecoder);
        if ($result["file"] instanceof \sndsgd\http\UploadedFile) {
            $result["file"] = [$result["file"]];
        }
        $this->assertSame(count($result["file"]), $expectCount);
    }

    public function providerParsePostFiles()
    {
        return [
            [
                [
                    "file" => [
                        "name" => "test.txt",
                        "type" => "",
                        "tmp_name" => "/tmp/phptmp",
                        "error" => 0,
                        "size" => 42,
                    ]
                ],
                1,
            ],
            [
                [
                    "file" => [
                        "name" => ["1.txt", "2.txt"],
                        "type" => ["1", "2"],
                        "tmp_name" => ["/tmp/1", "/tmp/2"],
                        "error" => [0, UPLOAD_ERR_INI_SIZE],
                        "size" => [1, 2],
                    ]
                ],
                2,
            ],
        ];
    }
}
