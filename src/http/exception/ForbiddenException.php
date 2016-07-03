<?php

namespace sndsgd\http\exception;

class ForbiddenException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::FORBIDDEN;
    }
}
