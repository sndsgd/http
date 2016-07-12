<?php

namespace sndsgd\http\data\decoder;

use \org\bovigo\vfs\vfsStream;

class JsonDecoderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->root = vfsStream::setup("root");
    }

    /**
     * @dataProvider providerDecode
     */
    public function testDecode($json, $type, $length, $options, $expect)
    {
        vfsStream::newFile("test.json")->at($this->root)->setContent($json);
        $path = vfsStream::url("root/test.json");
        $decoder = new JsonDecoder($path, $type, $length, $options);
        $this->assertSame($expect, $decoder->decode()); 
    }

    public function providerDecode()
    {
        $ret = [];

        $json = '{"test":1}';
        $ret[] = [
            $json,
            "application/json",
            strlen($json),
            null,
            ["test" => 1],
        ];

        $json = '[1,2,"a"]';
        $ret[] = [
            $json,
            "application/json",
            strlen($json),
            null,
            [1, 2, "a"],
        ];

        return $ret;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDecodeReadStreamException()
    {
        $path = vfsStream::url("root/test.json");
        (new JsonDecoder($path, "application/json", 1, null))->decode();
    }

    /**
     * @dataProvider providerDecodeException
     */
    public function testDecodeException(
        $json,
        $length,
        $options,
        $exception,
        $message
    )
    {
        $this->setExpectedException($exception, $message);
        vfsStream::newFile("test.json")->at($this->root)->setContent($json);
        $path = vfsStream::url("root/test.json");
        (new JsonDecoder($path, "application/json", $length, $options))->decode();
    }

    public function providerDecodeException()
    {
        $ret = [];

        # invalid json syntax
        $json = '{"test:"value",}';
        $ret[] = [
            $json,
            strlen($json),
            null, 
            "sndsgd\\http\\data\\DecodeException",
            "failed to decode JSON request data; Syntax error"
        ];

        # max depth exceeded
        $options = $this->getMockBuilder(DecoderOptions::class)
            ->setMethods(["getMaxNestingLevels"])
            ->getMock();
        $options->method("getMaxNestingLevels")->willReturn(3);

        $json = '{"a":{"b":{"c":{"d":5}}}}';
        $ret[] = [
            $json,
            strlen($json),
            $options, 
            "sndsgd\\http\\data\\DecodeException",
            "failed to decode JSON request data; Maximum stack depth exceeded"
        ];

        return $ret;
    }
}
