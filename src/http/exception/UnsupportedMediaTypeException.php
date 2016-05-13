<?php

namespace sndsgd\http\exception;

class UnsupportedMediaTypeException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 415;
}
