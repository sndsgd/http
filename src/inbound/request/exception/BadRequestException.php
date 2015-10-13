<?php

namespace sndsgd\http\inbound\request\exception;

use \sndsgd\http\inbound\request;


class BadRequestException extends request\ExceptionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $statusCode = 400;

    /**
     * @var array<string,mixed>
     */
    protected $validationErrors = [];

    /**
     * @param array<string,mixed>
     */
    public function setValidationErrors(array $errors)
    {
        $this->validationErrors = $errors;
    }

    /**
     * @return array<string,mixed>
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}
