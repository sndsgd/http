<?php

namespace sndsgd\http\exception;

class PreconditionRequiredException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::PRECONDITION_REQUIRED;
    }
}
