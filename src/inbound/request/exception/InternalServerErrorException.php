<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class InternalServerErrorException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 500;
}
