<?php

namespace sndsgd\http\exception;

class InsufficientStorageException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::INSUFFICIENT_STORAGE;
    }
}
