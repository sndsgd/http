<?php

namespace sndsgd\http\data\decoder;

use \org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \sndsgd\http\data\decoder\MultipartDataDecoder
 */
class MultipartDataDecoderTest extends \PHPUnit\Framework\TestCase
{
    use \phpmock\phpunit\PHPMock;

    const VFS_ROOT = "root";

    protected static $tempFiles = [];

    public static function tearDownAfterClass()
    {
        foreach (self::$tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function setup()
    {
        $this->root = vfsStream::setup(self::VFS_ROOT);
    }

    private function createMultipartTempFile(array $params)
    {
        $boundary = \sndsgd\Str::random(100);
        $multipart = new \GuzzleHttp\Psr7\MultipartStream($params, $boundary);

        $tempPath = tempnam(sys_get_temp_dir(), "uploaded-file-");
        file_put_contents($tempPath, $multipart);

        self::$tempFiles[] = $tempPath;

        return [
            $tempPath,
            "multipart/form-data; boundary=$boundary",
            filesize($tempPath),
        ];
    }

    private function getVfsTempPath($name = "test", $perms = 0777)
    {
        vfsStream::newFile($name, $perms)->at($this->root);
        return vfsStream::url(self::VFS_ROOT."/".$name);
    }

    /**
     * @covers ::decode
     * @expectedException \RuntimeException
     */
    public function testDecodeReadException()
    {
        $path = __FILE__."/does/not/exist.obviously";
        $type = "multipart/form-data; boundary=3ce079ead76547fa9261";
        $decoder = new MultipartDataDecoder($path, $type, 100);
        @$decoder->decode();
    }

    /**
     * @covers ::getBoundary
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
     * @covers ::getFieldHeader
     * @expectedException \sndsgd\http\data\DecodeException
     */
    public function testMalformedContentDispositionException()
    {
        list($path, $contentType, $length) = $this->createMultipartTempFile([
            [
                "name" => "test",
                "contents" => "value",
                "headers" => [
                    "Content-Disposition" => ";;;"
                ]
            ],
        ]);

        $decoder = new MultipartDataDecoder($path, $contentType, $length);
        @$decoder->decode();
    }

    /**
     * @covers ::getFieldHeader
     */
    public function testFileEmptyContentType()
    {
        list($path, $contentType, $length) = $this->createMultipartTempFile([
            [
                "name" => "file",
                "filename" => "test.txt",
                "contents" => "file contents...",
            ],
        ]);

        # remove the content type from the file header
        $remove = "Content-Type: text/plain\r\n";
        $contents = file_get_contents($path);
        $contents = str_replace($remove, "", $contents);
        file_put_contents($path, $contents);
        $length -= strlen($remove);

        $decoder = new MultipartDataDecoder($path, $contentType, $length);
        $result = $decoder->decode();
        $this->assertInstanceOf(\sndsgd\http\UploadedFile::class, $result["file"]);
    }

    public function testEmptyFileError()
    {
        list($path, $contentType, $length) = $this->createMultipartTempFile([
            [
                "name" => "file",
                "filename" => "test.txt",
                "contents" => "",
                "headers" => [
                    "Content-Type" => \sndsgd\Mime::getTypeFromExtension("txt"),
                ],
            ],
        ]);

        $decoder = new MultipartDataDecoder($path, $contentType, $length);
        $data = $decoder->decode();

        $this->assertArrayHasKey("file", $data);
        $file = $data["file"];
        $this->assertInstanceOf(\sndsgd\http\UploadedFile::class, $file);
        $error = $file->getError();
        $this->assertInstanceOf(\sndsgd\http\UploadedFileError::class, $error);
        $this->assertSame(UPLOAD_ERR_NO_FILE, $error->getCode());
    }

    /**
     * @dataProvider providerMaxFilesizeError
     */
    public function testMaxFilesizeError($maxSize, $size, $expectError)
    {
        list($path, $type, $length) = $this->createMultipartTempFile([
            [
                "name" => "file",
                "filename" => "test.txt",
                "contents" => \sndsgd\Str::random($size),
            ],
        ]);

        # replace options so the max filesize is less than 1024
        $options = $this->getMockBuilder(DecoderOptions::class)
            ->setMethods(["getMaxFileSize"])
            ->getMock();
        $options->method("getMaxFileSize")->willReturn($maxSize);

        $decoder = new MultipartDataDecoder($path, $type, $length, $options);
        $data = $decoder->decode();

        $this->assertArrayHasKey("file", $data);
        $file = $data["file"];
        $this->assertInstanceOf(\sndsgd\http\UploadedFile::class, $file);

        $error = $file->getError();
        if ($expectError) {
            $this->assertInstanceOf(\sndsgd\http\UploadedFileError::class, $error);
            $this->assertSame(UPLOAD_ERR_INI_SIZE, $error->getCode());
        } else {
            $this->assertNull($file->getError());
        }
    }

    public function providerMaxFilesizeError()
    {
        return [
            [100, 101, true],
            [100, 100, false],
            [100, 99, false],
        ];
    }

    /**
     * @dataProvider providerFieldsRemainExceptions
     */
    public function testFieldsRemainExceptions($feof, $fread, $exception)
    {
        $this->expectException($exception);

        list($path, $type, $length) = $this->createMultipartTempFile([
            [
                "name" => "file",
                "filename" => "test.txt",
                "contents" => "file contents",
            ],
        ]);

        $decoder = new MultipartDataDecoder($path, $type, $length);

        $feofMock = $this->getFunctionMock(__NAMESPACE__, "feof");
        $feofMock->expects($this->any())->willReturn($feof);

        $freadMock = $this->getFunctionMock(__NAMESPACE__, "fread");
        $freadMock->expects($this->any())->willReturn($fread);

        $rc = new \ReflectionClass($decoder);
        $fp = $rc->getProperty("fp");
        $fp->setAccessible(true);
        $fp->setValue($decoder, fopen($path, "r"));
        $buffer = $rc->getProperty("buffer");
        $buffer->setAccessible(true);
        $buffer->setValue($decoder, "");
        $lastBoundary = $rc->getProperty("lastBoundary");
        $lastBoundary->setAccessible(true);
        $lastBoundary->setValue($decoder, "a");

        $method = $rc->getMethod("fieldsRemain");
        $method->setAccessible(true);
        $method->invoke($decoder);
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
        list($path, $type, $length) = $this->createMultipartTempFile([
            [
                "name" => "file",
                "filename" => "test.txt",
                "contents" => "file contents",
            ],
        ]);

        $tempfile = $this->getVfsTempPath();

        $mock = $this->getMockBuilder(MultipartDataDecoder::class)
            ->setConstructorArgs([$path, $type, $length])
            ->setMethods(["getTempFilePath"])
            ->getMock();

        $mock->method("getTempFilePath")->willReturn($tempfile);

        $feofMock = $this->getFunctionMock(__NAMESPACE__, "feof");
        $feofMock->expects($this->any())->willReturn(true);

        # set the buffer to NOT contain the boundary
        $rc = new \ReflectionClass($mock);
        $property = $rc->getProperty("buffer");
        $property->setAccessible(true);
        $property->setValue($mock, \sndsgd\Str::random(1000));

        $mock->decode();
    }

    public function testReadUntil()
    {
        $bytesPerRead = 2048;
        $bytes = \sndsgd\Str::random($bytesPerRead * 3);
        list($path, $type, $length) = $this->createMultipartTempFile([
            [
                'name' => 'one',
                'contents' => $bytes,
            ],
            [
                "name" => "file",
                "filename" => "test.txt",
                "contents" => $bytes,
            ],
        ]);

        $decoder = new MultipartDataDecoder($path, $type, $length, null, $bytesPerRead);
        $result = $decoder->decode();
        $this->assertSame($bytes, $result["one"]);
        $this->assertSame($bytes, file_get_contents($result["file"]->getTempPath()));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetFileFromFieldOpenTempFileException()
    {
        list($path, $type, $length) = $this->createMultipartTempFile([
            [
                "name" => "file",
                "filename" => "test.txt",
                "contents" => "contents...",
            ],
        ]);

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
     * @dataProvider providerGetFileFromFieldWriteException
     * @expectedException \RuntimeException
     */
    public function testGetFileFromFieldWriteException(
        $firstWriteResult,
        $secondWriteResult
    )
    {
        list($path, $type, $length) = $this->createMultipartTempFile([
            [
                "name" => "file1",
                "filename" => "test.txt",
                "contents" => \sndsgd\Str::random(8192),
            ],
            [
                "name" => "file2",
                "filename" => "test.txt",
                "contents" => \sndsgd\Str::random(8192),
            ],
            [
                "name" => "file3",
                "filename" => "test.txt",
                "contents" => \sndsgd\Str::random(8192),
            ],
        ]);

        # replace options so the max filesize is less than 1024
        $options = $this->getMockBuilder(DecoderOptions::class)
            ->setMethods(["getMaxFileSize"])
            ->getMock();
        $options->method("getMaxFileSize")->willReturn(1024);

        $mock = $this->getMockBuilder(MultipartDataDecoder::class)
            ->setConstructorArgs([$path, $type, $length, $options])
            ->setMethods(["getTempFilePath"])
            ->getMock();

        $tempfile = $this->getVfsTempPath();
        $mock->method("getTempFilePath")->willReturn($tempfile);

        $fwriteMock = $this->getFunctionMock(__NAMESPACE__, "fwrite");
        $fwriteMock
            ->expects($this->any())
            ->will($this->onConsecutiveCalls($firstWriteResult, $secondWriteResult));

        $mock->decode();
    }

    public function providerGetFileFromFieldWriteException()
    {
        return [
            [false, true],
            [true, false],
        ];
    }

    /**
     *
     *
     */
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
