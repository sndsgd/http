<?php

namespace sndsgd\http\exception;

class InsufficientStorageException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::INSUFFICIENT_STORAGE;
    }
}
