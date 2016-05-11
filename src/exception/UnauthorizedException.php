<?php

namespace sndsgd\http\exception;

class UnauthorizedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 401;
}
