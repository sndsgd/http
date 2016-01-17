<?php

namespace sndsgd\http\data;

/**
 * The base class for request body decoders
 */
abstract class DecoderAbstract
{
    /**
     * The path to the stream to decode
     *
     * @var string
     */
    protected $path;

    /**
     * The type of content to decode
     *
     * @var string
     */
    protected $contentType;

    /**
     * The length for the content to decode
     *
     * @var int
     */
    protected $contentLength;

    /**
     * An object containing ini settings related to body decoding
     *
     * @var \sndsgd\http\data\DecoderOptions
     */
    protected $options;

    /**
     * A collection of decoded values
     *
     * @var array<string,mixed>
     */
    protected $values;

    /**
     * @param \sndsgd\http\inbound\Request $request
     * @param string $path
     */
    public function __construct(
        string $path,
        string $contentType,
        int $contentLength,
        DecoderOptions $options = null
    )
    {
        $this->path = $path;
        $this->contentType = $contentType;
        $this->contentLength = $contentLength;
        $this->options = $options ?? new DecoderOptions();
        $this->values = new \sndsgd\http\data\Collection(
            $this->options->getMaxVars(),
            $this->options->getMaxNestingLevels()
        );
    }

    /**
     * Decode the data in the path, and return the result as an array
     *
     * @return array
     * @throws Exception If the data cannot be decoded
     */
    abstract public function decode(): array;
}
