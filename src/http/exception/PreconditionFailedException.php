<?php

namespace sndsgd\http\exception;

class PreconditionFailedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::PRECONDITION_FAILED;
    }
}
