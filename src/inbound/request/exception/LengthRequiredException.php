<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class LengthRequiredException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 411;
}
