<?php

namespace sndsgd\http\exception;

class BadRequestException extends ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    const STATUS_CODE = 400;

    /**
     * @var array<\sndsgd\ErrorInterface>
     */
    protected $errors;

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
