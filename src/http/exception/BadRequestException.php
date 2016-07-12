<?php

namespace sndsgd\http\exception;

class BadRequestException extends ExceptionAbstract
{
    /**
     * @var array<\sndsgd\ErrorInterface>
     */
    protected $errors;

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return \sndsgd\http\Status::BAD_REQUEST;
    }

    /**
     * @param array<\sndsgd\ErrorInterface>
     */
    public function setErrors(array $errors)
    {
        $this->errors = \sndsgd\TypeTest::typedArray(
            $errors,
            \sndsgd\ErrorInterface::class
        );
    }

    /**
     * @return array<\sndsgd\ErrorInterface>
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
