<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class NotFoundException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 404;
}
