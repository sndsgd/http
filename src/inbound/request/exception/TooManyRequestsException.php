<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class TooManyRequestsException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 429;
}
