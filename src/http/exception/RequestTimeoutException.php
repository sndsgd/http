<?php

namespace sndsgd\http\exception;

class RequestTimeoutException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 408;
}
