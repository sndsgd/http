<?php

namespace sndsgd\http\exception;

class GoneException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::GONE;
    }
}
