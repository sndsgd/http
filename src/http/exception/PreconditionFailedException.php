<?php

namespace sndsgd\http\exception;

class PreconditionFailedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 412;
}
