<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\UploadedFile
 */
class UploadedFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @dataProvider providerConstructor
     */
    public function testConstructor(
        $clientFilename,
        $contentType,
        $size,
        $tempPath
    )
    {
        $file = new UploadedFile($clientFilename, $contentType, $size, $tempPath);
        $rc = new \ReflectionClass($file);

        $property = $rc->getProperty("clientFilename");
        $property->setAccessible(true);
        $this->assertSame($clientFilename, $property->getValue($file));

        $property = $rc->getProperty("contentType");
        $property->setAccessible(true);
        $this->assertSame($contentType, $property->getValue($file));

        $property = $rc->getProperty("size");
        $property->setAccessible(true);
        $this->assertSame($size, $property->getValue($file));

        $property = $rc->getProperty("tempPath");
        $property->setAccessible(true);
        $this->assertSame($tempPath, $property->getValue($file));
    }

    public function providerConstructor()
    {
        return [
            ["test.txt", "text/plain", 12345, ""],
            ["test.txt", "text/plain", 12345, "/tmp/upload-aH6fT"],
        ];
    }

    /**
     * @covers ::__destruct
     */
    public function testDestruct()
    {
        $tempPath = tempnam(sys_get_temp_dir(), "UploadedFile-");
        file_put_contents($tempPath, "testing...");

        $file = new UploadedFile("", "", filesize($tempPath), $tempPath);
        unset($file);
    }

    /**
     * @covers ::getClientFilename
     * @dataProvider providerGetClientFilename
     */
    public function testGetClientFilename($clientFilename)
    {
        $file = new UploadedFile($clientFilename, "text/plain", 123, "");
        $this->assertSame($clientFilename, $file->getClientFilename());
    }

    public function providerGetClientFilename()
    {
        return [
            [""],
            ["test.txt"],
        ];
    }

    /**
     * @covers ::getContentType
     * @dataProvider providerGetContentType
     */
    public function testGetContentType($contentType, $expect)
    {
        $file = new UploadedFile("test.txt", $contentType, 123, "");
        $this->assertSame($expect, $file->getContentType());
    }

    public function providerGetContentType()
    {
        return [
            ["", ""],
            ["text/plain", "text/plain"],
            ["TEXT/PLAIN", "text/plain"],
        ];
    }

    /**
     * @covers ::isType
     * @dataProvider providerIsType
     */
    public function testIsType($contentType, $types, $expect)
    {
        $file = new UploadedFile("test.txt", $contentType, 123, "");
        $this->assertSame($expect, $file->isType($types));
    }

    public function providerIsType()
    {
        return [
            ["image/jpeg", ["image/jpeg"], true],
            ["IMAGE/JPEG", ["image/jpeg"], true],
            ["image/jpeg", ["jpg"], true],
            ["image/jpeg", ["png", "gif", "jpg"], true],
            ["app/evil", ["image/png", "image/gif", "image/jpeg"], false],
        ];
    }

    /**
     * @covers ::getSize
     * @dataProvider providerGetSize
     */
    public function testGetSize($size, $expect)
    {
        $file = new UploadedFile("test.txt", "text/plain", $size, "");
        $this->assertSame($expect, $file->getSize());
    }

    public function providerGetSize()
    {
        return [
            [123456, 123456],
        ];
    }

    /**
     * @covers ::getTempPath
     * @dataProvider providerGetTempPath
     */
    public function testGetTempPath($tempPath, $expect, $exception = null)
    {
        $file = new UploadedFile("test.txt", "text/plain", 123, $tempPath);
        if ($exception === null) {
            $this->assertSame($expect, $file->getTempPath());    
        }
        else {
            $this->setExpectedException($exception);
            $file->getTempPath();
        }   
    }

    public function providerGetTempPath()
    {
        return [
            ["/test/path", "/test/path"],
            ["", null, "RuntimeException"],
        ];
    }
}