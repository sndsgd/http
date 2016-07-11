<?php

namespace sndsgd\http\exception;

class GoneException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::GONE;
    }
}
