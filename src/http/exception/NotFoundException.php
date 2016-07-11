<?php

namespace sndsgd\http\exception;

class NotFoundException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::NOT_FOUND;
    }
}
