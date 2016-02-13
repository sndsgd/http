<?php

namespace sndsgd\http\data;

use \sndsgd\DataTrait;
use \sndsgd\ErrorTrait;

/**
 * The base class for request data encoders
 */
abstract class EncoderAbstract
{
    use DataTrait, ErrorTrait;

    /**
     * When the data has been encoded, it'll be stashed here
     *
     * @var string
     */
    protected $encodedData;

    /**
     * Encode data to a string
     *
     * @return boolean Indicates if the operation was successfull
     */
    abstract public function encode();

    /**
     * @return string
     */
    public function getEncodedData()
    {
        if (
            $this->encodedData === null &&
            !$this->encode()
        ) {
            throw new \Exception("failed to encode data; ".$this->getError());
        }
        return $this->encodedData;
    }
}
