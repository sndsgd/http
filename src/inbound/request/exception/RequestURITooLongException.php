<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class RequestURITooLongException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 414;
}
