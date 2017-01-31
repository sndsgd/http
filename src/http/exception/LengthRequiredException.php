<?php

namespace sndsgd\http\exception;

class LengthRequiredException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::LENGTH_REQUIRED;
    }
}
