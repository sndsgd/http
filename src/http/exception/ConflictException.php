<?php

namespace sndsgd\http\exception;

class ConflictException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::CONFLICT;
    }
}
