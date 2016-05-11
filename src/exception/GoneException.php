<?php

namespace sndsgd\http\exception;

class GoneException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 410;
}
