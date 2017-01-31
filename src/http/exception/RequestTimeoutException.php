<?php

namespace sndsgd\http\exception;

class RequestTimeoutException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::REQUEST_TIMEOUT;
    }
}
