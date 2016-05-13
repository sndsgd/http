<?php

namespace sndsgd\http\exception;

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
    public function getStatusCode(): int
    {
        return static::STATUS_CODE;
    }
}
