<?php

namespace sndsgd\http\data\decoder;

/**
 * A urlendocde content decoder
 */
class UrlDecoder extends \sndsgd\http\data\DecoderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function decode(): array
    {
        $querystring = @file_get_contents($this->path, true);
        if ($querystring === false) {
            throw new \RuntimeException("Failed to read input stream");
        }

        $decoder = new QueryStringDecoder($this->contentLength, $this->values);
        return $decoder->decode($querystring)->getValues();
    }
}
