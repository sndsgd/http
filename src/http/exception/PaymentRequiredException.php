<?php

namespace sndsgd\http\exception;

class PaymentRequiredException extends ExceptionAbstract
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::PAYMENT_REQUIRED;
    }
}
