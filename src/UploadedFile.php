<?php

namespace sndsgd\http;

class UploadedFile implements \JsonSerializable
{
    use \sndsgd\ErrorTrait;

    protected $clientFilename;
    protected $contentType;
    protected $size;
    protected $tempPath;

    public function __construct(
        string $clientFilename,
        string $contentType,
        int $size,
        string $tempPath = ""
    )
    {
        $this->clientFilename = $clientFilename;
        $this->contentType = strtolower($contentType);
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

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function isType(array $types): bool
    {
        foreach ($types as $type) {
            $type = strtolower($type);
            if (
                $type === $this->contentType ||
                \sndsgd\Mime::getTypeFromExtension($type) === $this->contentType
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
            "clientFilename" => $this->clientFilename,
            "contentType" => $this->contentType,
            "size" => $this->size,
            "tempPath" => $this->tempPath,
        ];
    }
}
