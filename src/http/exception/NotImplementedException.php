<?php

namespace sndsgd\http\exception;

class NotImplementedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::NOT_IMPLEMENTED;
    }
}
