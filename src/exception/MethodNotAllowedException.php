<?php

namespace sndsgd\http\exception;

class MethodNotAllowedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 405;
}
