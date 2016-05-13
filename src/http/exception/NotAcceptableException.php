<?php

namespace sndsgd\http\exception;

class NotAcceptableException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 406;
}
