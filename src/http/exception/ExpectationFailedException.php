<?php

namespace sndsgd\http\exception;

class ExpectationFailedException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::EXPECTATION_FAILED;
    }
}
