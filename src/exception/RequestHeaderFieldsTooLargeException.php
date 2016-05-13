<?php

namespace sndsgd\http\exception;

class RequestHeaderFieldsTooLargeException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 431;
}
