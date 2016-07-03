<?php

namespace sndsgd\http\exception;

class RequestTimeoutException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::REQUEST_TIMEOUT;
    }
}
