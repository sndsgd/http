<?php

namespace sndsgd\http\exception;

class ProxyAuthenticationRequiredException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::PROXY_AUTHENTICATION_REQUIRED;
    }
}
