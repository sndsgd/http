<?php

namespace sndsgd\http\test\runner;

use \sndsgd\http\test\SerialTest;

class SerialRunner
{
    protected $tests = [];

    public function __construct()
    {

    }

    public function addTest(string $class)
    {
        if (isset($this->tests[$class])) {
            throw new \Exception(
                "failed to add serial test '$class'; ".
                "this test has already been registered"
            );
        }

        // if (!is_a($class, SerialTest::class)) {
        //     throw new \Exception(
        //         "invalid value provided for 'class'; ".
        //         "expecting the name of a class that extends ".SerialTest::class
        //     );
        // }

        $rc = new \ReflectionClass($class);
        $method = $rc->getMethod("provide");
        if (!$method || !$method->isStatic() || !$method->isPublic()) {
            throw new \Exception(
                "failed to create testcase for '$class'; serial tests ".
                "require `public static function provide(): array`"
            );
        }
        
        $parameters = $class::provide();
        if (count($parameters) === 0) {
            echo "skipping serial test '$class'\n";
            return false;
        }

        $tests[$class] = [$rc, $parameters];
        return true;
    }

    /**
     * Get the next request for the next test
     *
     * @return function [description]
     */
    public function getBatch(int $i): array
    {

    }
}
