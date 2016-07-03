<?php

namespace sndsgd\http\exception;

class LoopDetectedException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::LOOP_DETECTED;
    }
}
