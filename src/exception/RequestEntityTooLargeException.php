<?php

namespace sndsgd\http\exception;

class RequestEntityTooLargeException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 413;
}
