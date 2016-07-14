<?php

namespace sndsgd\http\data\decoder;

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
     * @param int $contentLength
     * @param \sndsgd\http\data\Collection|null $values
     */
    public function __construct(
        int $contentLength,
        \sndsgd\http\data\Collection $values = null
    )
    {
        $this->contentLength = $contentLength;
        if ($values === null) {
            $options = new DecoderOptions();
            $this->values = new \sndsgd\http\data\Collection(
                $options->getMaxVars(),
                $options->getMaxNestingLevels()
            );
        } else {
            $this->values = $values;
        }
    }

    /**
     * Decode a urlencoded parameter key value pair
     *
     * @param string $keyValuePair
     * @return array<string> The decoded key and value
     */
    public function decodePair(string $keyValuePair): array
    {
        # more than a handful of clients like to use '+' characters
        # when encoding data that is (almost) compliant with rfc 3986
        # this is a hack that allows for it
        if (strpos($keyValuePair, "+") !== false) {
            $keyValuePair = str_replace("+", " ", $keyValuePair);
        }

        $parts = explode("=", $keyValuePair, 2);
        $key = rawurldecode($parts[0]);

        # interpret a key with an empty value as `null`
        $value = (count($parts) === 1) ? null : rawurldecode($parts[1]);
        return [$key, $value];
    }

    /**
     * Decode a query string
     *
     * @param string $query
     * @return array
     */
    public function decode($query): array
    {
        foreach (explode("&", $query) as $keyValuePair) {
            list($key, $value) = $this->decodePair($keyValuePair);
            $this->values->addValue($key, $value);
        }
        return $this->values->getValues();
    }
}
