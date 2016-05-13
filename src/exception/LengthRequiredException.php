<?php

namespace sndsgd\http\exception;

class LengthRequiredException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 411;
}
