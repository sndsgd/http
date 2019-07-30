<?php

namespace sndsgd\http\exception;

class BadRequestExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider providerSetGetErrors
     */
    public function testSetGetErrors(array $errors)
    {
        $ex = new BadRequestException();
        $ex->setErrors($errors);
        $this->assertSame($errors, $ex->getErrors());
    }

    public function providerSetGetErrors()
    {
        return [
            [
                [
                    new \sndsgd\Error("error 1", 1),
                    new \sndsgd\Error("error 2", 2),
                ],
            ],
        ];
    }
}
