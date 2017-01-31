<?php

namespace sndsgd\http\exception;

class InternalServerErrorException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::INTERNAL_SERVER_ERROR;
    }
}
