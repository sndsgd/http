<?php

namespace sndsgd\http\exception;

class ExpectationFailedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 417;
}
