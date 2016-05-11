<?php

namespace sndsgd\http\exception;

class ConflictException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 409;
}
