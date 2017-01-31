<?php

namespace sndsgd\http\exception;

class RequestUriTooLongException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::REQUEST_URI_TOO_LONG;
    }
}
