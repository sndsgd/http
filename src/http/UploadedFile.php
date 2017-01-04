<?php

namespace sndsgd\http;

class UploadedFile implements \JsonSerializable
{
    /**
     * The name of the file as provided by the client's device
     *
     * @var string
     */
    protected $clientFilename;

    /**
     * The content type as provided by the client's device
     *
     * @var string
     */
    protected $unverifiedContentType;

    /**
     * The content type as determined by a call to `finfo()`
     *
     * @var string
     */
    protected $contentType;

    /**
     * The size of the file in bytes
     *
     * @var int
     */
    protected $size;

    /**
     * The absolute path to the temp file
     * Note: this may be empty if the upload failed
     *
     * @var string
     */
    protected $tempPath;

    /**
     * If an error is encountered, it will be accessible here
     *
     * @var \sndsgd\ErrorInterface
     */
    protected $error;


    public function __construct(
        string $clientFilename,
        string $unverifiedContentType,
        int $size = 0,
        string $tempPath = "",
        int $errorCode = UPLOAD_ERR_OK
    )
    {
        $this->clientFilename = $clientFilename;
        $this->unverifiedContentType = strtolower($unverifiedContentType);
        $this->size = $size;
        $this->tempPath = $tempPath;
        if ($errorCode !== UPLOAD_ERR_OK) {
            $this->error = new \sndsgd\http\UploadedFileError($errorCode);
        }
    }

    /**
     * Remove the temp file when the object is destroyed
     */
    public function __destruct()
    {
        if ($this->tempPath !== "" && file_exists($this->tempPath)) {
            unlink($this->tempPath);
        }
    }

    /**
     * Retrieve the filename provided by the client
     *
     * @return string
     */
    public function getClientFilename(): string
    {
        return $this->clientFilename;
    }

    /**
     * Get the content type
     *
     * @param bool $allowUnverified
     * @return string
     */
    public function getContentType(bool $allowUnverified = false): string
    {
        # always use the verified content type if it exists
        if ($this->contentType === null) {
            if ($allowUnverified) {
                return $this->unverifiedContentType;
            }
            $this->contentType = $this->getContentTypeFromFile($this->tempPath);
        }
        return $this->contentType;
    }

    /**
     * Stubbable method to read the content type from a file
     *
     * @param string $path
     * @return string
     */
    protected function getContentTypeFromFile(string $path)
    {
        return \sndsgd\Mime::getTypeFromFile($path);
    }

    /**
     * Determine whether the file type matches any that are provided
     *
     * @param array<string> $mimeTypes Acceptable mime types
     * @param bool $allowUnverified Whether to trust the client provided type
     * @return bool
     */
    public function isType(
        array $mimeTypes,
        bool $allowUnverified = false
    ): bool
    {
        $contentType = $this->getContentType($allowUnverified);
        foreach ($mimeTypes as $type) {
            if (strtolower($type) === $contentType) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve the byte size of the file
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Retrieve a human readable version of the size
     *
     * @param int $precision The number of decimal places to show
     * @param string $point The decimal point
     * @param string $sep The thousands separator
     * @return string
     */
    public function getFormattedSize(
        int $precision = 0,
        string $point = ".",
        string $sep = ","
    ): string
    {
        return \sndsgd\Fs::formatSize($this->size, $precision, $point, $sep);
    }

    /**
     * Retrieve the absolute path to the temp file
     *
     * @return string
     * @throws \RuntimeException If the temp path was not set
     */
    public function getTempPath(): string
    {
        if ($this->tempPath === "") {
            throw new \RuntimeException("failed to retrieve uploaded file path");
        }
        return $this->tempPath;
    }

    /**
     * @return \sndsgd\ErrorInterface|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get an array representation of this object
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            "clientFilename" => $this->getClientFilename(),
            "unverifiedContentType" => $this->getContentType(true),
            "verifiedContentType" => $this->getContentType(),
            "size" => $this->getSize(),
            "tempPath" => $this->getTempPath(),
        ];
    }

    /**
     * @see http://php.net/manual/jsonserializable.jsonserialize.php
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
