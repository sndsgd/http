<?php

namespace sndsgd\http\exception;

class TooManyRequestsException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::TOO_MANY_REQUESTS;
    }
}
