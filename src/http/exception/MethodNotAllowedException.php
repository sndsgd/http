<?php

namespace sndsgd\http\exception;

class MethodNotAllowedException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::METHOD_NOT_ALLOWED;
    }
}
