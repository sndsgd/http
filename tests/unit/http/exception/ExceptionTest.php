<?php

namespace sndsgd\http\exception;

/**
 * Finds and tests all the exception classes `getStatusCode` methods
 */
class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider providerGetStatusCode
     */
    public function testGetStatusCode(array $classes)
    {
        foreach ($classes as $class) {
            $ex = new $class();
            $this->assertTrue(is_int($ex->getStatusCode()));
        }
    }

    public function providerGetStatusCode()
    {
        $filter = function(\ReflectionClass $class): bool {
            return (
                $class->isSubclassOf(ExceptionAbstract::class) &&
                !$class->isAbstract()
            );
        };

        $locator = new \sndsgd\fs\locator\ClassLocator($filter);
        $locator->searchDir(__DIR__."/../../../../src/http/exception");
        return [[$locator->getClasses()]];
    }
}
