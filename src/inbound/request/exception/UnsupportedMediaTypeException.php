<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class UnsupportedMediaTypeException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 415;
}
