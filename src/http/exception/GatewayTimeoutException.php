<?php

namespace sndsgd\http\exception;

class GatewayTimeoutException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 504;
}
