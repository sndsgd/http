<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class LoopDetectedException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 508;
}
