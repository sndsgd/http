<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class InsufficientStorageException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 507;
}
