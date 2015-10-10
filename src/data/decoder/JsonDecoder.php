<?php

namespace sndsgd\http\data\decoder;

use \Exception;


class JsonDecoder extends \sndsgd\http\data\Decoder
{
    /**
     * {@inheritdoc}
     */
    public function getDecodedData()
    {
        $data = json_decode(file_get_contents($this->path), true);
        if ($data === null) {
            throw new Exception("Failed to parse request data as JSON", 400);
        }
        return $data;
    }
}