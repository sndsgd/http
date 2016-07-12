<?php

namespace sndsgd\http\exception;

class LengthRequiredException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::LENGTH_REQUIRED;
    }
}
