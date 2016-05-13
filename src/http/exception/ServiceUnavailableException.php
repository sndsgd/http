<?php

namespace sndsgd\http\exception;

class ServiceUnavailableException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 503;
}
