<?php

namespace sndsgd\http\exception;

class PaymentRequiredException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::PAYMENT_REQUIRED;
    }
}
