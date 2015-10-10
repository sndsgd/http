<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class BandwidthLimitExceededException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 509;
}
