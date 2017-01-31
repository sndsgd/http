<?php

namespace sndsgd\http\exception;

class HttpVersionNotSupportedException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::HTTP_VERSION_NOT_SUPPORTED;
    }
}
