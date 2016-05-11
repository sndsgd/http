<?php

namespace sndsgd\http\exception;

class RequestedRangeNotSatisfiableException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 416;
}
