<?php

namespace sndsgd\http\exception;

class ExceptionAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStatusCode()
    {
        $mock = $this->getMockForAbstractClass(ExceptionAbstract::class);
        $this->assertSame(500, $mock->getStatusCode());
    }
}
