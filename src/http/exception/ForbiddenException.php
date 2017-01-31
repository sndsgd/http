<?php

namespace sndsgd\http\exception;

class ForbiddenException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::FORBIDDEN;
    }
}
