<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class NetworkAuthenticationRequiredException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 511;
}
