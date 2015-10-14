<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class GatewayTimeoutException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 504;
}
