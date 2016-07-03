<?php

namespace sndsgd\http\exception;

class BandwidthLimitExceededException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::BANDWIDTH_LIMIT_EXCEEDED;
    }
}
