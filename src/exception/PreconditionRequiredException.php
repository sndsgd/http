<?php

namespace sndsgd\http\exception;

class PreconditionRequiredException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 428;
}
