<?php

namespace sndsgd\http\exception;

class UnauthorizedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::UNAUTHORIZED;
    }
}
