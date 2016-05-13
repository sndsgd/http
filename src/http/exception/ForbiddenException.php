<?php

namespace sndsgd\http\exception;

class ForbiddenException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 403;
}
