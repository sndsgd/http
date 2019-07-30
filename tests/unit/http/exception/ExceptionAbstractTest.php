<?php

namespace sndsgd\http\exception;

class ExceptionAbstractTest extends \PHPUnit\Framework\TestCase
{
    public function testGetStatusCode()
    {
        $mock = $this->getMockForAbstractClass(ExceptionAbstract::class);
        $this->assertSame(500, $mock->getStatusCode());
    }
}
