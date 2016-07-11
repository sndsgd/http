<?php

namespace sndsgd\http\exception;

abstract class ExceptionAbstract extends \Exception
{
    /**
     * Get the status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::INTERNAL_SERVER_ERROR;
    }
}
