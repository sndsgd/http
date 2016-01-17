<?php

namespace sndsgd\http;

class Data
{
    protected static $bodyContentTypes = [
        "application/json" => "sndsgd\\http\\data\\decoder\\JsonDecoder",
        "multipart/form-data" => "sndsgd\\http\\data\\decoder\\MultipartDataDecoder",
        "application/x-www-form-urlencoded" => "sndsgd\\http\\data\\decoder\\UrlDecoder",
    ];

    public static function decodeBody(string $contentType, int $contentLength)
    {
        $pos = strpos($contentType, ";");
        $type = ($pos === false) ? $contentType : substr($contentType, 0, $pos);
        $type = strtolower($type);

        if (isset(static::$bodyContentTypes[$type])) {
            $class = static::$bodyContentTypes[$type];
            $decoder = new $class("php://input", $contentType, $contentLength);
            return $decoder->decode();
        }
    }
}
