<?php

namespace sndsgd\http\exception;

class ConflictException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::CONFLICT;
    }
}
