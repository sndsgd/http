<?php

namespace sndsgd\http\exception;

class ExceptionAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStatusCode()
    {
        $mock = $this->getMockForAbstractClass(ExceptionAbstract::class);
        $this->assertSame(ExceptionAbstract::STATUS_CODE, $mock->getStatusCode());
    }
}
