<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class PreconditionRequiredException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 428;
}
