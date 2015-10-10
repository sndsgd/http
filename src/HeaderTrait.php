<?php

namespace sndsgd\http;

use \sndsgd\Arr;


trait HeaderTrait
{
    /**
     * @var array<string,string>
     */
    protected $headers = [];

    /**
     * Set a header
     *
     * @param string $key
     * @param string|integer $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[strtolower($key)] = $value;
    }

    /**
     * Set all headers
     *
     * @param array<string,string> $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = [];
        $this->addHeaders($headers);
    }

    /**
     * Add a request header
     *
     * @param string $key
     * @param string|integer $value
     */
    public function addHeader($key, $value)
    {
        Arr::addValue($this->headers, strtolower($key), $value);
    }

    /**
     * Add multiple headers
     *
     * @param array<string,string> $headers
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }
    }

    /**
     * Get a header value
     *
     * @param string|null $key A single header to fetch, or null to return all
     * @return array|string|integer|float|null
     */
    public function getHeader($key)
    {
        $key = strtolower($key);
        return (array_key_exists($key, $this->headers))
            ? $this->headers[$key]
            : null;
    }

    /**
     * @return array<string,string>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Retrieve an array of headers as strings
     *
     * @return array<string>
     */
    public function stringifyHeaders()
    {
        $ret = [];
        foreach ($this->headers as $header => $value) {
            if (is_array($value)) {
                $value = implode(", ", $value);
            }
            $ret[] = "$header: $value";
        }
        return $ret;
    }
}
