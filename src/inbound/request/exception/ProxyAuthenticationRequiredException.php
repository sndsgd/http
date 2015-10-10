<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class ProxyAuthenticationRequiredException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 407;
}
