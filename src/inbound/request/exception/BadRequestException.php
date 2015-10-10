<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class BadRequestException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 400;
}
