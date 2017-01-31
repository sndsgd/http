<?php

namespace sndsgd\http\exception;

class NetworkAuthenticationRequiredException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::NETWORK_AUTHENTICATION_REQUIRED;
    }
}
