<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class RequestHeaderFieldsTooLargeException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 431;
}
