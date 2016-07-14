<?php

namespace sndsgd\http\data\decoder;

/**
 * A JSON request body decoder
 */
class JsonDecoder extends DecoderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function decode(): array
    {
        $json = @file_get_contents($this->path, true);
        if ($json === false) {
            throw new \RuntimeException(
                \sndsgd\Error::createMessage("failed to read input stream")
            );
        }

        $maxNestingLevels = $this->options->getMaxNestingLevels();
        $data = json_decode($json, true, $maxNestingLevels);
        if ($data === null) {
            $message = json_last_error_msg();
            throw new \sndsgd\http\data\DecodeException(
                "failed to decode JSON request data; $message"
            );
        }
        return $data;
    }
}
