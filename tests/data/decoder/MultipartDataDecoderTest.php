<?php

namespace sndsgd\http\data\decoder;

use \Exception;
use \sndsgd\fs\File;


class MultipartDataDecoderTest extends \PHPUnit_Framework_TestCase
{
    protected static $dir;
    protected static $fileMd5;

    public static function setupBeforeClass()
    {
        self::$dir = __DIR__."/multipart-data";
        self::$fileMd5 = md5_file(self::$dir."/test.png");
    }

    public function parseFile($name)
    {
        $file = new File(self::$dir."/$name.boundary");
        if (
            !$file->test(File::EXISTS | File::READABLE) ||
            ($boundary = $file->read()) === false
        ) {
            throw new Exception("error in ".__CLASS__."\n".$file->getError()."\n");
        }

        $_SERVER["CONTENT_TYPE"] = "multipart/form-data; boundary=$boundary";

        $parser = new MultipartDataDecoder;
        $parser->setPath(self::$dir."/$name.raw");
        return $parser->getDecodedData();
    }

    private function examineResults($result, $isFile = false)
    {
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey("name", $result);
        $this->assertEquals("asd", $result["name"]);
        $this->assertArrayHasKey("email", $result);
        $this->assertEquals("asd@asd.com", $result["email"]);
        $this->assertArrayHasKey("submit", $result);
        $this->assertEquals("submit", $result["submit"]);

        $this->assertArrayHasKey("file", $result);
        if ($isFile === false) {
            $this->assertNull($result["file"]);
        }
        else {
            $file = $result["file"];
            $this->assertEquals(self::$fileMd5, md5_file($file->getTempPath()));
        }
    }

    public function testChrome()
    {
        $this->examineResults($this->parseFile("chrome"));
        $this->examineResults($this->parseFile("chrome-file"), true);
    }

    public function testSafari()
    {
        $result = $this->parseFile("safari");
        $this->examineResults($result);

        $result = $this->parseFile("safari-file");
        $this->examineResults($result, true);
    }

    public function testFirefox()
    {
        $result = $this->parseFile("firefox");
        $this->examineResults($result);

        $result = $this->parseFile("firefox-file");
        $this->examineResults($result, true);
    }

    public function testInternetExplorer9()
    {
        $result = $this->parseFile("ie9");
        $this->examineResults($result);

        $result = $this->parseFile("ie9-file");
        $this->examineResults($result, true);
    }
}