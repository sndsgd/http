<?php

namespace sndsgd\http\exception;

class BandwidthLimitExceededException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 509;
}
