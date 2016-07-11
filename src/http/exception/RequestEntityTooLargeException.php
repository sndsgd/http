<?php

namespace sndsgd\http\exception;

class RequestEntityTooLargeException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::REQUEST_ENTITY_TOO_LARGE;
    }
}
