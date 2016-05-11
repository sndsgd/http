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
    protected $validationErrors;

    /**
     * @param array<\sndsgd\ErrorInterface>
     */
    public function setValidationErrors(array $errors)
    {
        $this->validationErrors = \sndsgd\TypeTest::typedArray(
            $errors,
            \sndsgd\ErrorInterface::class
        );
    }

    /**
     * @return array<\sndsgd\ErrorInterface>
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}
