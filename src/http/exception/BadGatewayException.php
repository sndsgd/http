<?php

namespace sndsgd\http\exception;

class BadGatewayException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::BAD_GATEWAY;
    }
}
