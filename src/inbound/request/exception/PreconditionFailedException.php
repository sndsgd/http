<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class PreconditionFailedException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 412;
}
