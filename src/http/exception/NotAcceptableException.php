<?php

namespace sndsgd\http\exception;

class NotAcceptableException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::NOT_ACCEPTABLE;
    }
}
