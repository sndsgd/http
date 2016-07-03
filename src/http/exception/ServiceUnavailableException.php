<?php

namespace sndsgd\http\exception;

class ServiceUnavailableException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::SERVICE_UNAVAILABLE;
    }
}
