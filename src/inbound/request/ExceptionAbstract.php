<?php

namespace sndsgd\http\inbound\request;


abstract class ExceptionAbstract extends \Exception
{
    /**
     * The relevant http status code
     *
     * @var integer
     */
    protected $statusCode = 500;

    /**
     * Get the status code
     *
     * @return integer
     */
    public function getStatusCode()/*: int */
    {
        return $this->statusCode;
    }

    /**
     * Get data suitable for a response body
     * 
     * @return array<string,mixed>
     */
    public function getResponseData()/*: array */
    {
        return [
            "message" => $this->getMessage(),
            "payload" => null
        ];
    }
}
