<?php

namespace sndsgd\http\inbound\request;


abstract class ExceptionAbstract extends \Exception
{
    /**
     * The relevant http status code
     *
     * @var integer
     */
    const STATUS_CODE = 500;

    /**
     * Get the status code
     *
     * @return integer
     */
    public function getStatusCode()/*: int */
    {
        return static::STATUS_CODE;
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
