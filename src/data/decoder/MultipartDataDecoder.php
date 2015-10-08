<?php

namespace sndsgd\http\data\decoder;

use \Exception;
use \sndsgd\Arr;
use \sndsgd\http\UploadedFile;
use \sndsgd\Mime;


class MultipartDataDecoder extends \sndsgd\http\data\Decoder
{
    const CONTENT_DISPOSITION_REGEX =
        '/content-disposition:[ +]?form-data;[ +](?:name="(.*?)")?(?:;[ +]?filename="(.*?)")?/i';

    /**
     * The number of bytes to read when using `fread()`
     *
     * @var integer
     */
    const BYTES_PER_READ = 8192;

    /**
     * The input stream to read multipart data from
     *
     * @var resource
     */
    private $fp;

    /**
     * The multipart field boundary
     *
     * @var string
     */
    private $boundary;

    /**
     * The final field boundary, with the end dashes
     *
     * @var string
     */
    private $lastBoundary;

    /**
     * The contents of the input stream that have been read, but not processed
     *
     * @var string
     */
    private $buffer = "";

    /**
     * The values found in the multipart data
     *
     * @var array
     */
    private $values = [];

    /**
     * The files that were found in the input data
     *
     * @var array
     */
    private $files = [];


    /**
     * {@inheritdoc}
     */
    public function getDecodedData()
    {
        $this->detectBoundary();
        $this->fp = fopen($this->path, "r");
        if ($this->fp === false) {
            throw new Exception("failed to open '{$this->path}' for reading");
        }
        return $this->decode();
    }

    private function detectBoundary()
    {
        # get the boundary string from the content type header
        $pos = strpos($_SERVER["CONTENT_TYPE"], "boundary=");
        if ($pos === false) {
            $err = "missing value for 'boundary' in content-type header";
            throw new Exception($err);
        }
        $this->boundary = "--".substr($_SERVER["CONTENT_TYPE"], $pos + 9);
        $this->lastBoundary = "{$this->boundary}--";
    }

    /**
     * Process the fields in the input stream
     *
     * @return array<string,mixed>
     */
    protected function decode()
    {
        while ($this->fieldsRemain() === true) {
            list($name, $filename, $contentType) = $this->getFieldHeader();

            # if the current field is not a file process it as a normal value
            if ($filename === null) {
                $value = $this->getValueFromField();
            }

            # otherwise, the current field is a file
            # copy its contents to a temp file
            else {
                $value = $this->getFileFromField($name, $filename, $contentType);
            }

            Arr::addValue($this->values, $name, $value);
        }

        fclose($this->fp);
        return $this->values;
    }

    /**
     * Read the input stream into the buffer until a string is encountered
     *
     * @param string $search The string to read until
     * @return integer The position of the string in the buffer
     */
    private function readUntil($search)
    {
        while (($position = strpos($this->buffer, $search)) === false) {
            if (feof($this->fp)) {
                throw new Exception("unexpected end of input stream", 400);
            }
            $this->buffer .= fread($this->fp, self::BYTES_PER_READ);
        }
        return $position;
    }

    /**
     * Determine if any more fields remain in the stream
     *
     * @return boolean
     */
    private function fieldsRemain()
    {
        $bufferlen = strlen($this->buffer);
        $minlen = strlen($this->lastBoundary);

        # if the buffer is too short to contain the last boundary
        # read enough bytes into the buffer to allow for a strpos test
        if ($bufferlen < $minlen) {
            if (feof($this->fp)) {
                throw new Exception("unexpected end of input stream", 400);
            }
            if (($bytes = fread($this->fp, self::BYTES_PER_READ)) === false) {
                $err = "failed to read $minlen bytes from input stream";
                throw new Exception($err, 400);
            }
            $this->buffer .= $bytes;
        }

        # if the buffer starts with the last boundary, there are no more fields
        return (strpos($this->buffer, $this->lastBoundary) !== 0);
    }

    /**
     * Read the header values for the current field from the input stream
     *
     * @return array
     */
    private function getFieldHeader()
    {
        # read the input stream until the empty line after the header
        $position = $this->readUntil("\r\n\r\n");

        # separate the header from the field content
        # remove the header content from the buffer
        $header = substr($this->buffer, 0, $position);
        $this->buffer = substr($this->buffer, $position + 4);

        if (preg_match(
            self::CONTENT_DISPOSITION_REGEX,
            $header,
            $matches
        ) !== 1) {
            $err = "missing Content-Disposition header in field block";
            throw new Exception($err);
        }

        # get rid of the whole content disposition match
        # create the array to return
        array_shift($matches);
        $ret = array_pad($matches, 3, null);
        # [0] name
        # [1] filename
        # [2] contentType

        # if a filename was in the content disposition
        # attempt to find its content type in the field header
        # then attempt find its content type by analyzing the filename
        # then give up; its a bad request
        if ($ret[1] !== null) {
            if (preg_match(
                '/content-type:[ +]?(.*)(?:;|$)/mi',
                $header,
                $matches
            ) === 1) {
                $ret[2] = $matches[1];
            }
            else if (
                ($ext = pathinfo($ret[1], PATHINFO_EXTENSION)) &&
                ($mimeType = Mime::getTypeFromExtension($ext)) !== null
            ) {
                $ret[2] = $mimeType;
            }
            else {
                $err = "missing Content-Type header in file field block";
                throw new Exception($err, 400);
            }
        }

        return $ret;
    }

    /**
     * Get the value of the current field in the input stream
     *
     * @return string
     */
    private function getValueFromField()
    {
        $position = $this->readUntil($this->boundary);

        # there is always a newline after the value and before the boundary
        # exclude that newline from the value
        $value = substr($this->buffer, 0, $position - 2);

        # update the buffer to exclude the value and the pre boundary newline
        $this->buffer = substr($this->buffer, $position);

        return $value;
    }

    /**
     * Copy file contents from the input stream to a temp file
     *
     * @param string $name The field name
     * @param string $filename The name of the uploaded file
     * @param string $contentType The mime content type of the file
     * @return array|number
     * @return array containing 2 elements [temPath, filesize]
     * @return integer UPLOAD_ERR_* if an error was encountered
     */
    private function getFileFromField($name, $filename, $contentType)
    {
        $uploadedFile = new UploadedFile($name, $filename, $contentType);
        $tempPath = $uploadedFile->getTempPath();
        $tempFile = fopen($tempPath, "w");
        if ($tempFile === false) {
            $err = "failed to open file for writing '$tempPath'";
            return $uploadedFile->setError($err);
        }

        # number of bytes read from the input stream in the last loop cycle
        $bytesRead = 0;
        # the total number of bytes written to the temp file
        $bytesWritten = 0;

        # if anything is left over from the previous field, add it to the file
        if ($this->buffer) {
            if (($bytesRead = fwrite($tempFile, $this->buffer)) === false) {
                $err = "fwrite() failed to write to '$tempPath'";
                return $uploadedFile->setError($err);
            }
            $bytesWritten += $bytesRead;
        }

        while (($pos = strpos($this->buffer, $this->boundary)) === false) {
            $this->buffer = fread($this->fp, self::BYTES_PER_READ);
            if (($bytesRead = fwrite($tempFile, $this->buffer)) === false) {
                $err = "fwrite() failed to write to '$tempPath'";
                return $uploadedFile->setError($err);
            }
            $bytesWritten += $bytesRead;
        }

        # update the size of the file based on the boundary position
        $size = $bytesWritten - $bytesRead + $pos - 2;
        if ($size === 0) {
            fclose($tempFile);
            unlink($tempPath);
            return null;
        }

        ftruncate($tempFile, $size);
        fclose($tempFile);

        # copy the excess contents of the local buffer to the object buffer
        $this->buffer = substr($this->buffer, $pos);

        # update the uploaded file instance
        return $uploadedFile->setSize($size);
    }
}