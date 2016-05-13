<?php

namespace sndsgd\http\exception;

class PaymentRequiredException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 402;
}
