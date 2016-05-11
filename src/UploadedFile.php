<?php

namespace sndsgd\http;

class UploadedFile
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
        string $tempPath = ""
    )
    {
        $this->clientFilename = $clientFilename;
        $this->unverifiedContentType = strtolower($unverifiedContentType);
        $this->size = $size;
        $this->tempPath = $tempPath;
    }

    public function __destruct()
    {
        if ($this->tempPath !== "" && file_exists($this->tempPath)) {
            unlink($this->tempPath);
        }
    }

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

    public function isType(
        array $types,
        bool $allowUnverified = false
    ): bool
    {
        $contentType = $this->getContentType($allowUnverified);
        foreach ($types as $type) {
            if (strtolower($type) === $contentType) {
                return true;
            }
        }
        return false;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getTempPath(): string
    {
        if ($this->tempPath === "") {
            throw new \RuntimeException("failed to retrieve uploaded file path");
        }
        return $this->tempPath;
    }

    public function setError(\sndsgd\ErrorInterface $error): UploadedFile
    {
        $this->error = $error;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }
}
