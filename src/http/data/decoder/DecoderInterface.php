<?php

namespace sndsgd\http\data\decoder;

interface DecoderInterface
{
    /**
     * Decode the data in the path, and return the result as an array
     *
     * @return array
     * @throws \sndsgd\http\data\DecodeException If the decode fails
     */
    public function decode(): array;
}
