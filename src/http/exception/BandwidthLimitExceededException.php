<?php

namespace sndsgd\http\exception;

class BandwidthLimitExceededException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::BANDWIDTH_LIMIT_EXCEEDED;
    }
}
