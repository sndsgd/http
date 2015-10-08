<?php

namespace sndsgd\http\outbound;

use \InvalidArgumentException;
use \sndsgd\http\Code;
use \sndsgd\http\HeaderTrait;


/**
 * Base class for outbound responses
 */
class Response
{
    use HeaderTrait;

    /**
     * The http status code
     *
     * @var integer
     */
    protected $statusCode = 200;

    /**
     * The http status text
     *
     * @var string
     */
    protected $statusText = "OK";

    /**
     * @param integer $code An http status code
     * @see \sndsgd\http\Code
     */
    public function setStatusCode($code, $statusText = null)
    {
        $this->statusCode = $code;
        $this->statusText = ($statusText === null)
            ? Code::getStatusText($code)
            : $statusText;

        if ($this->statusText === null) {
            throw new InvalidArgumentException("invalid HTTP status code '$code'");
        }
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }
}