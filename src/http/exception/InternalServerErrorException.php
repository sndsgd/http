<?php

namespace sndsgd\http\exception;

class InternalServerErrorException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::INTERNAL_SERVER_ERROR;
    }
}
