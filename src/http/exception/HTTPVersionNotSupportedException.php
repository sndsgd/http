<?php

namespace sndsgd\http\exception;

class HttpVersionNotSupportedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 505;
}
