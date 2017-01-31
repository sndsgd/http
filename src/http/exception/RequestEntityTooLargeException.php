<?php

namespace sndsgd\http\exception;

class RequestEntityTooLargeException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::REQUEST_ENTITY_TOO_LARGE;
    }
}
