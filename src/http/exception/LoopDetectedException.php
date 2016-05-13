<?php

namespace sndsgd\http\exception;

class LoopDetectedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 508;
}
