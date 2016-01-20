<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class ServiceUnavailableException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 503;
}