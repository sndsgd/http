<?php

namespace sndsgd\http\request;

use \sndsgd\http\data\decoder;
use \sndsgd\http\exception;

class BodyDecoder
{
    /**
     * A map of content types and decoder classnames
     * 
     * @var array<string,string>
     */
    protected static $decoders = [
        "application/json" => decoder\JsonDecoder::class,
        "multipart/form-data" => decoder\MultipartDataDecoder::class,
        "application/x-www-form-urlencoded" => decoder\UrlDecoder::class,
    ];

    /**
     * Replace existing decoders, or add additional decoders
     *
     * @param string $class The nme of the decoder class
     * @param string ...$contentTypes The content types the decoder can handle
     */
    public static function addDecoder(string $class, string ...$contentTypes)
    {
        $interface = decoder\DecoderInterface::class;
        if (!is_subclass_of($class, $interface)) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'class'; ".
                "expecting the name of a class that implements '$interface'"
            );
        }

        foreach ($contentTypes as $type) {
            static::$decoders[$class] = $type;
        }
    }

    public function decode(
        string $method,
        string $stream,
        string $contentType,
        int $contentLength,
        decoder\DecoderOptions $options = null
    )
    {
        $options = $options ?? new decoder\DecoderOptions();

        # if the `enable_post_data_reading` ini setting is on
        # use the already parsed $_POST and $_FILES data
        if (
            $method === "POST" && 
            !\sndsgd\Str::beginsWith($contentType, "application/json") &&
            $options->getPostDataReadingEnabled()
        ) {
            return $this->parsePost();
        }

        $decoder = $this->getDecoder(
            $stream,
            $contentType,
            $contentLength,
            $options
        );
        return $decoder->decode();
    }

    /**
     * Stubbable method for creating a decoder instance
     * 
     * @param string $stream 
     * @param string $contentType
     * @param int $contentLength
     * @param \sndsgd\http\data\DecoderOptions $options
     * @return \sndsgd\http\data\decoder\DecoderInterface
     * @throws \sndsgd\http\exception\BadRequestException
     */
    protected function getDecoder(
        string $stream,
        string $contentType,
        int $contentLength,
        decoder\DecoderOptions $options
    )
    {
        $type = \sndsgd\Str::before($contentType, ";");
        if (!isset(static::$decoders[$type])) {
            throw new exception\BadRequestException(
                "failed to decode request body; ".
                "unknown content-type '$contentType'"
            );
        }

        $class = static::$decoders[$type];
        return new $class($stream, $contentType, $contentLength, $options);
    }

    /**
     * Process the $_POST superglobal to spoof the results of a decoder
     *
     * @return array
     */
    protected function parsePost()
    {
        $ret = $_POST ?: [];
        if (isset($_FILES)) {
            foreach ($_FILES as $name => $info) {
                if (is_array($info["name"])) {
                    $len = count($info["name"]);
                    for ($i = 0; $i < $len; $i++) {
                        $file = $this->createUploadedFile([
                            "name" => $info["name"][$i] ?? "",
                            "type" => $info["type"][$i] ?? "",
                            "tmp_name" => $info["tmp_name"][$i] ?? "",
                            "error" => $info["error"][$i] ?? 0,
                            "size" => $info["size"][$i] ?? 0,
                        ]);
                        \sndsgd\Arr::addValue($ret, $name, $file);    
                    }
                } else {
                    $file = $this->createUploadedFile($info);
                    \sndsgd\Arr::addValue($ret, $name, $file);
                }
            }
        }
        return $ret;
    }

    protected function createUploadedFile(array $info): \sndsgd\http\UploadedFile
    {
        if (!isset($info["type"]) || empty($info["type"])) {
            $extension = pathinfo($info["name"], PATHINFO_EXTENSION);
            $info["type"] = \sndsgd\Mime::getTypeFromExtension($extension);
        }

        $file = new \sndsgd\http\UploadedFile(
            $info["name"],
            $info["type"],
            $info["size"],
            $info["tmp_name"]
        );

        if ($info["error"] !== UPLOAD_ERR_OK) {
            $error = new \sndsgd\http\UploadedFileError($info["error"]);
            $file->setError($error);
        }

        return $file;
    }
}
