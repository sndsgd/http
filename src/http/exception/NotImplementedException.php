<?php

namespace sndsgd\http\exception;

class NotImplementedException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::NOT_IMPLEMENTED;
    }
}
