<?php

namespace sndsgd\http\exception;

class ExpectationFailedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::EXPECTATION_FAILED;
    }
}
