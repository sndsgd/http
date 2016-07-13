<?php

namespace sndsgd\http\data\decoder;

/**
 * The base class for request body decoders
 */
abstract class DecoderAbstract implements DecoderInterface
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
     * @param string $path
     * @param string $contentType
     * @param int $contentLength
     * @param \sndsgd\http\data\decoder\DecoderOptions|null $options
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
}
