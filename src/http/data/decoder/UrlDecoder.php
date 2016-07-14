<?php

namespace sndsgd\http\data\decoder;

/**
 * A urlencoded content decoder
 */
class UrlDecoder extends DecoderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function decode(): array
    {
        $querystring = @file_get_contents($this->path, true);
        if ($querystring === false) {
            throw new \RuntimeException(
                \sndsgd\Error::createMessage("failed to read input stream")
            );
        }

        $decoder = new QueryStringDecoder($this->contentLength, $this->values);
        return $decoder->decode($querystring);
    }
}
