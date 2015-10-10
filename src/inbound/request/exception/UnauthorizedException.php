<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class UnauthorizedException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 401;
}
