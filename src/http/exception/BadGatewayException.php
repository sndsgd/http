<?php

namespace sndsgd\http\exception;

class BadGatewayException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::BAD_GATEWAY;
    }
}
