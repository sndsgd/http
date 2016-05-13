<?php

namespace sndsgd\http\exception;

class RequestUriTooLongException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 414;
}
