<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class GoneException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 410;
}
