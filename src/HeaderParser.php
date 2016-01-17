<?php

namespace sndsgd\http;

class HeaderParser
{
    /**
     * The http protocol
     *
     * @var string
     */
    protected $protocol;

    /**
     * The http status code
     *
     * @var integer
     */
    protected $statusCode;

    /**
     * The http status message
     *
     * @var string
     */
    protected $statusText;

    /**
     * The fields in the header
     *
     * @var string
     */
    protected $fields;

    /**
     * Parse a header string
     *
     * @param string $header
     * @return
     */
    public function parse($header)
    {
        $parts = explode("\r\n", $header, 2);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'header'; expecting a string ".
                "that utilizes \\r\\n line breaks"
            );
        }

        $statusLine = preg_split("/\s+/", $parts[0], 3);
        $this->protocol = $statusLine[0];
        $this->statusCode = intval($statusLine[1]);
        $this->statusText = $statusLine[2];
        $ret = 0;
        $this->fields = [];
        foreach (explode("\r\n", trim($parts[1])) as $line) {
            list($key, $value) = explode(":", $line, 2);
            \sndsgd\Arr::addvalue($this->fields, strtolower($key), trim($value));
            $ret++;
        }
        return $ret;
    }

    /**
     * Get the protocol
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Get the status code
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get the status text
     *
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }

    /**
     * Get the header fields
     *
     * @return array<string,mixed>
     */
    public function getFields()
    {
        return $this->fields;
    }
}
