<?php

namespace sndsgd\http\exception;

class GatewayTimeoutException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::GATEWAY_TIMEOUT;
    }
}
