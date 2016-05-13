<?php

namespace sndsgd\http\exception;

class InternalServerErrorException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 500;
}
