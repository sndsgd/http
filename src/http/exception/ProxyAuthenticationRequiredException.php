<?php

namespace sndsgd\http\exception;

class ProxyAuthenticationRequiredException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 407;
}
