<?php

namespace sndsgd\http\exception;

class NotFoundException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 404;
}
