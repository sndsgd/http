<?php

namespace sndsgd\http\exception;

class BadGatewayException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 502;
}
