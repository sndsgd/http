<?php

namespace sndsgd\http\exception;

class RequestedRangeNotSatisfiableException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::REQUESTED_RANGE_NOT_SATISFIABLE;
    }
}
