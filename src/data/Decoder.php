<?php

namespace sndsgd\http\data;


/**
 * The base class for request data encoders
 */
abstract class Decoder
{
    /**
     * The path to a file or stream to parse from
     *
     * @var string
     */
    protected $path = "php://input";

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Decode the data in the path, and return the result as an array
     *
     * @return array
     * @throws Exception If the data cannot be decoded
     */
    abstract public function getDecodedData();
}