<?php

namespace sndsgd\http\exception;

class PreconditionFailedException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::PRECONDITION_FAILED;
    }
}
