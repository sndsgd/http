<?php

namespace sndsgd\http\data\decoder;

use \sndsgd\http\data\Collection;
use \sndsgd\http\data\DecoderOptions;

class QueryStringDecoder
{
    /**
     * The request body content length
     * To be used in the future for processing large request bodies in
     * a memory efficient fashion
     *
     * @var int
     */
    protected $contentLength;

    /**
     * A collection of decoded parameters
     *
     * @var \sndsgd\http\data\Collection
     */
    protected $values;

    /**
     * Create a query string decoder
     * Provide a parameter collection to append to it; otherwise all decoded
     * parameters will be added to a new collection
     *
     * @param string $contentType
     * @param \sndsgd\http\data\Collection|null $params
     */
    public function __construct(
        int $contentLength,
        Collection $values = null
    )
    {
        $this->contentLength = $contentLength;
        if ($values === null) {
            $options = new DecoderOptions();
            $this->values = $values ?: new Collection(
                $options->getMaxVars(),
                $options->getMaxNestingLevels()
            );
        } else {
            $this->values = $values;
        }
    }

    /**
     * Decode a urlencoded parameter pair
     *
     * @param string $pair
     */
    public function decodePair(string $pair)
    {
        # more than a handful of clients like to use '+' characters
        # when encoding data that is (almost) compliant with rfc 3986
        # this is a hack that allows for it
        if (strpos($pair, "+") !== false) {
            $pair = str_replace("+", " ", $pair);
        }

        $parts = explode("=", $pair, 2);
        $key = rawurldecode($parts[0]);

        # interpret a key with an empty value as `null`
        $value = (count($parts) === 1) ? null : rawurldecode($parts[1]);
        return [$key, $value];
    }

    /**
     * Decode a query string
     *
     * @param string $query
     * @return \sndsgd\http\data\Collection
     */
    public function decode($query)
    {
        foreach (explode("&", $query) as $pair) {
            list($key, $value) = $this->decodePair($pair);
            $this->values->addValue($key, $value);
        }
        return $this->values;
    }
}
