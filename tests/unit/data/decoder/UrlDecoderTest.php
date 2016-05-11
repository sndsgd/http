<?php

namespace sndsgd\http\data\decoder;

use \org\bovigo\vfs\vfsStream;

class UrlDecoderTest extends \PHPUnit_Framework_TestCase
{
    const CONTENT_TYPE = "application/x-www-form-urlencoded";

    public function setup()
    {
        $this->root = vfsStream::setup("root");
    }

    /**
     * @dataProvider providerDecode
     */
    public function testDecode($content, $type, $length, $options, $expect)
    {
        vfsStream::newFile("test")->at($this->root)->setContent($content);
        $path = vfsStream::url("root/test");
        $decoder = new UrlDecoder($path, $type, $length, $options);
        $this->assertSame($expect, $decoder->decode()); 
    }

    public function providerDecode()
    {
        $querystring = 'test=value';
        return [
            [
                $querystring,
                self::CONTENT_TYPE,
                strlen($querystring),
                null,
                ["test" => "value"],
            ],
        ];
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDecodeReadStreamException()
    {
        $path = vfsStream::url("root/test");
        $decoder = new UrlDecoder($path, self::CONTENT_TYPE, 1, null);
        $decoder->decode();
    }
}
