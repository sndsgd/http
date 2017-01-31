<?php

namespace sndsgd\http\exception;

class UnauthorizedException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::UNAUTHORIZED;
    }
}
