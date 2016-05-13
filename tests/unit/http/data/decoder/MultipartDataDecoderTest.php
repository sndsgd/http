<?php

namespace sndsgd\http\data\decoder;

use \org\bovigo\vfs\vfsStream;

class MultipartDataDecoderTest extends \PHPUnit_Framework_TestCase
{
    const VFS_ROOT = "root";

    public function setup()
    {
        $this->root = vfsStream::setup(self::VFS_ROOT);
    }

    private function getVfsTempPath($name = "test", $perms = 0777)
    {
        vfsStream::newFile($name, $perms)->at($this->root);
        return vfsStream::url(self::VFS_ROOT."/".$name);
    }

    private function getDir($name = null)
    {
        $dir = realpath(__DIR__."/../../../data");
        return ($name === null) ? $dir : "$dir/$name";
    }

    private function getParameterDetails()
    {
        $path = $this->getDir()."/parameters.json";
        $json = file_get_contents($path);
        $data = json_decode($data, true);
        exit;
    }

    private function getTestUploadInfo($name)
    {
        $dir = $this->getDir("multipart");
        $contentFile = "$dir/$name.content";
        $typeFile =  "$dir/$name.type";
        return [
            $contentFile,
            file_get_contents($typeFile),
            filesize($contentFile),
        ];  
    }

    private function getTestFileInfo($name)
    {
        $dir = $this->getDir("multipart/various");
        $contentFile = "$dir/$name.content";
        $typeFile =  "$dir/$name.type";
        return [
            $contentFile,
            file_get_contents($typeFile),
            filesize($contentFile),
        ];
    }


    /**
     * @expectedException Exception
     */
    public function testDecodeReadException()
    {
        $path = __FILE__."/does/not/exist.obviously";
        $type = "multipart/form-data; boundary=3ce079ead76547fa9261";
        $decoder = new MultipartDataDecoder($path, $type, 100);
        @$decoder->decode();
    }

    /**
     * @expectedException \sndsgd\http\data\DecodeException
     */
    public function testGetBoundaryException()
    {
        $path = __FILE__;
        $type = "multipart/form-data";
        $decoder = new MultipartDataDecoder($path, $type, 100);
        @$decoder->decode();
    }

    /**
     * @expectedException \sndsgd\http\data\DecodeException
     */
    public function testMalformedContentDispositionException()
    {
        $path = $this->getDir("multipart")."/malformed-content-disposition.content";
        $type = "multipart/form-data; boundary=3ce079ead76547fa9261ae19db4febb3";
        $decoder = new MultipartDataDecoder($path, $type, 100);
        @$decoder->decode();
    }

    public function testEmptyFileError()
    {
        list($path, $type, $size) = $this->getTestUploadInfo("empty-file");

        $decoder = new MultipartDataDecoder($path, $type, $size);
        $data = $decoder->decode();

        $this->assertArrayHasKey("file", $data);
        $file = $data["file"];
        $this->assertInstanceOf(\sndsgd\http\UploadedFile::class, $file);
        $error = $file->getError();
        $this->assertInstanceOf(\sndsgd\http\UploadedFileError::class, $error);
        $this->assertSame(UPLOAD_ERR_NO_FILE, $error->getCode());
    }

    public function testMaxFilesizeError()
    {
        list($path, $type, $size) = $this->getTestUploadInfo("two-images");

        # replace options so the max filesize is less than 1024
        $options = $this->getMockBuilder(DecoderOptions::class)
            ->setMethods(["getMaxFileSize"])
            ->getMock();
        $options->method("getMaxFileSize")->willReturn(1024);

        $decoder = new MultipartDataDecoder($path, $type, $size, $options);
        $data = $decoder->decode();

        # `random` exceeds the max file size
        $this->assertArrayHasKey("random", $data);
        $file = $data["random"];
        $this->assertInstanceOf(\sndsgd\http\UploadedFile::class, $file);
        $error = $file->getError();
        $this->assertInstanceOf(\sndsgd\http\UploadedFileError::class, $error);
        $this->assertSame(UPLOAD_ERR_INI_SIZE, $error->getCode());

        # `qr` doess not exceed the max file size
        $this->assertArrayHasKey("qr", $data);
        $file = $data["qr"];
        $this->assertInstanceOf(\sndsgd\http\UploadedFile::class, $file);
        $this->assertNull($file->getError());
    }

    /**
     * @dataProvider providerFieldsRemainExceptions
     */
    public function testFieldsRemainExceptions($feof, $fread, $exception)
    {
        $this->setExpectedException($exception);

        list($path, $type, $size) = $this->getTestUploadInfo("empty-file");

        $mock = $this->getMockBuilder(MultipartDataDecoder::class)
            ->setConstructorArgs([$path, $type, $size])
            ->setMethods(["feof", "fread"])
            ->getMock();

        $mock->method("feof")->willReturn($feof);
        $mock->method("fread")->willReturn($fread);

        $rc = new \ReflectionClass($mock);
        $fp = $rc->getProperty("fp");
        $fp->setAccessible(true);
        $fp->setValue($mock, fopen($path, "r"));
        $buffer = $rc->getProperty("buffer");
        $buffer->setAccessible(true);
        $buffer->setValue($mock, "");
        $lastBoundary = $rc->getProperty("lastBoundary");
        $lastBoundary->setAccessible(true);
        $lastBoundary->setValue($mock, "a");

        $method = $rc->getMethod("fieldsRemain");
        $method->setAccessible(true);
        $method->invoke($mock);
    }

    public function providerFieldsRemainExceptions()
    {
        return [
            [true, true, \sndsgd\http\data\DecodeException::class],
            [false, false, \RuntimeException::class],
        ];
    }


    /**
     * @expectedException \sndsgd\http\data\DecodeException
     */
    public function testReadUntilException()
    {
        list($path, $type, $length) = $this->getTestUploadInfo("empty-file");

        $tempfile = $this->getVfsTempPath();

        $mock = $this->getMockBuilder(MultipartDataDecoder::class)
            ->setConstructorArgs([$path, $type, $length])
            ->setMethods(["getTempFilePath", "feof"])
            ->getMock();

        $mock->method("getTempFilePath")->willReturn($tempfile);
        $mock->method("feof")->willReturn(true);

        # set the buffer to NOT contain the boundary
        $rc = new \ReflectionClass($mock);
        $property = $rc->getProperty("buffer");
        $property->setAccessible(true);
        $property->setValue($mock, \sndsgd\Str::random(1000));

        $mock->decode();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetFileFromFieldOpenTempFileException()
    {
        list($path, $type, $length) = $this->getTestUploadInfo("empty-file");

        $tempfile = $this->getVfsTempPath("test", 0000);

        $mock = $this->getMockBuilder(MultipartDataDecoder::class)
            ->setConstructorArgs([$path, $type, $length])
            ->setMethods(["getTempFilePath"])
            ->getMock();

        $mock->method("getTempFilePath")->willReturn($tempfile);

        # suppress the fopen warnings
        @$mock->decode();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetFileFromFieldWriteException()
    {
        list($path, $type, $length) = $this->getTestUploadInfo("empty-file");

        $tempfile = $this->getVfsTempPath();

        $mock = $this->getMockBuilder(MultipartDataDecoder::class)
            ->setConstructorArgs([$path, $type, $length])
            ->setMethods(["getTempFilePath", "fwrite"])
            ->getMock();

        $mock->method("getTempFilePath")->willReturn($tempfile);
        $mock->method("fwrite")->willReturn(false);

        # set the buffer to NOT contain the boundary
        $rc = new \ReflectionClass($mock);
        $property = $rc->getProperty("buffer");
        $property->setAccessible(true);
        $property->setValue($mock, \sndsgd\Str::random(1000));

        @$mock->decode();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetFileFromFieldWriteException2()
    {
        list($path, $type, $length) = $this->getTestUploadInfo("empty-file");

        $tempfile = $this->getVfsTempPath();

        $mock = $this->getMockBuilder(MultipartDataDecoder::class)
            ->setConstructorArgs([$path, $type, $length])
            ->setMethods(["getTempFilePath", "fwrite"])
            ->getMock();

        $mock->method("getTempFilePath")->willReturn($tempfile);
        $mock->method("fwrite")->willReturn(false);

        # set the buffer to NOT contain the boundary
        $rc = new \ReflectionClass($mock);
        $property = $rc->getProperty("buffer");
        $property->setAccessible(true);
        $property->setValue($mock, "");

        @$mock->decode();
    }

    public function testFileUploadError()
    {
        $stream = $this->getVfsTempPath("stream");
        $tempfile = $this->getVfsTempPath("temp");

        $decoder = new MultipartDataDecoder($stream, "", 123);

        $rc = new \ReflectionClass($decoder);
        $method = $rc->getMethod("fileUploadError");
        $method->setAccessible(true);
        $file = $method->invoke(
            $decoder,
            UPLOAD_ERR_PARTIAL,
            $tempfile,
            fopen($tempfile, "r"),
            "name",
            "content/type",
            123
        );

        $this->assertFalse(file_exists($tempfile));
        $this->assertInstanceOf(\sndsgd\http\UploadedFile::class, $file);
        $this->assertInstanceOf(\sndsgd\http\UploadedFileError::class, $file->getError());
    }
}
