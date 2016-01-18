<?php

namespace sndsgd\http;

class UploadedFile implements \JsonSerializable
{
    protected $clientFilename;
    protected $unverifiedContentType;
    protected $contentType;
    protected $size;
    protected $tempPath;
    protected $error;

    public function __construct(
        string $clientFilename,
        string $unverifiedContentType,
        int $size,
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

    public function getContentType(bool $allowUnverified = false): string
    {
        # always use the verified content type if it exists
        if ($this->contentType === null) {
            if ($allowUnverified) {
                return $this->unverifiedContentType;
            }
            $this->contentType = \sndsgd\Mime::getTypeFromFile($this->tempPath);
        }
        return $this->contentType;
    }

    public function isType(
        array $types,
        bool $allowUnverified = false
    ): bool
    {
        $contentType = $this->getContentType($allowUnverified);
        foreach ($types as $type) {
            $type = strtolower($type);
            if (
                $type === $contentType ||
                \sndsgd\Mime::getTypeFromExtension($type) === $contentType
            ) {
                return true;
            }
        }
        return false;
    }

    public function getSize()
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

    public function jsonSerialize()
    {
        return [
            "error" => $this->error,
            "clientFilename" => $this->clientFilename,
            "contentType" => $this->getContentType(true),
            "size" => $this->size,
            "tempPath" => $this->tempPath,
        ];
    }

    public function setError(string $error)
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }
}
