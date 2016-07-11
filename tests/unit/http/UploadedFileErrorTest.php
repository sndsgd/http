<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\UploadedFileError
 */
class UploadedFileErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @dataProvider providerConstructor
     */
    public function testConstructor($code, $exception = "")
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }

        $error = new UploadedFileError($code);
    }

    public function providerConstructor()
    {
        return [
            [UPLOAD_ERR_OK, \InvalidArgumentException::class],
            [UPLOAD_ERR_INI_SIZE, ""],
            [UPLOAD_ERR_FORM_SIZE, ""],
            [UPLOAD_ERR_PARTIAL, ""],
            [UPLOAD_ERR_NO_FILE, ""],
            [UPLOAD_ERR_NO_TMP_DIR, ""],
            [UPLOAD_ERR_CANT_WRITE, ""],
            [UPLOAD_ERR_EXTENSION, ""],
            [42, \InvalidArgumentException::class],
        ];
    }
}
