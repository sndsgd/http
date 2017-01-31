<?php

namespace sndsgd\http\exception;

class UnsupportedMediaTypeException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::UNSUPPORTED_MEDIA_TYPE;
    }
}
