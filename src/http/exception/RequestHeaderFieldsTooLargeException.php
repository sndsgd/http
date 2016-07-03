<?php

namespace sndsgd\http\exception;

class RequestHeaderFieldsTooLargeException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::REQUEST_HEADER_FIELDS_TOO_LARGE;
    }
}
