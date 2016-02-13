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
     * A map of `lowercase key` => `key`
     *
     * @var array<string,string>
     */
    protected $headerKeyMap = [];

    /**
     * Set a header
     *
     * @param string $key
     * @param string $value
     */
    public function setHeader(string $key, string $value)
    {
        $key = $this->getHeaderKey($key, true);
        $this->headers[$key] = $value;
    }

    private function getHeaderKey(string $key, bool $set = false): string
    {
        $lowercaseKey = strtolower($key);
        if ($lowercaseKey !== $key) {
            if (isset($this->headerKeyMap[$lowercaseKey])) {
                return $this->headerKeyMap[$lowercaseKey];
            }
            elseif ($set) {
                $this->headerKeyMap[$lowercaseKey] = $key;    
            }
        }
        return $key;
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
    public function addHeader(string $key, string $value)
    {
        $key = $this->getHeaderKey($key, true);
        Arr::addValue($this->headers, $key, $value);
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
     * @return array<string>|string
     */
    public function getHeader(string $key)
    {
        $key = $this->getHeaderKey($key);
        return $this->headers[$key] ?? null;
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
