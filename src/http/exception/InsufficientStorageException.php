<?php

namespace sndsgd\http\exception;

class InsufficientStorageException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 507;
}
