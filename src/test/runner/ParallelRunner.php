<?php

namespace sndsgd\http\test\runner;

use \sndsgd\http\test\ParallelTest;
use \sndsgd\http\test\TestCase;

class ParallelRunner
{
    protected $tests = [];

    public function __construct()
    {

    }

    public function execute(int $concurrentRequests = 5)
    {
        while ($tests = array_splice($this->tests, 0, $concurrentRequests)) {
            $requests = [];
            foreach ($tests as $test) {
                $requests[] = $test->getRequest();
            }

            $responses = $this->executeMultipleRequests($requests);
            foreach ($tests as $index => $test) {
                $test->execute($responses[$index]);
            }
            //sleep(1);
        }
    }

    public function executeMultipleRequests(array $requests)
    {
        $len = count($requests);
        if ($len === 0) {
            return [];
        }

        $curlHandles = [];
        $mh = curl_multi_init();
        foreach ($requests as $request) {
            $ch = $request->getCurl();
            $curlHandles[] = $ch;
            curl_multi_add_handle($mh, $ch);
        }

        do {
            curl_multi_exec($mh, $xferActive);
            curl_multi_select($mh, .1);
        }
        while ($xferActive);

        foreach ($requests as $request) {
            $ch = array_shift($curlHandles);
            $body = curl_multi_getcontent($ch);
            $info = curl_getinfo($ch);
            curl_multi_remove_handle($mh, $ch);
            $ret[] = $request->createResponse($body, $info);
        }
        curl_multi_close($mh);
        return $ret;
    }

    public function addTestClass(string $class)
    {
        if (isset($this->tests[$class])) {
            throw new \Exception(
                "failed to add test '$class'; ".
                "this test has already been registered"
            );
        }

        if (is_a())


        $methods = $this->getTestMethods($class);
        foreach ($methods as list($testMethod, $requestMethod, $provideMethod)) {
            $instance = new $class();
            $requests = [];

            if ($provideMethod) {
                $sets = $provideMethod->invoke($instance);
                foreach ($sets as $args) {
                    $request = $requestMethod->invokeArgs($instance, $args);
                    $this->tests[] = new TestCase(
                        $instance,
                        $testMethod,
                        $request,
                        $args
                    );
                }
            }
            else {
                $request = $requestMethod->invoke($instance);
                $this->tests[] = new TestCase(
                    $instance,
                    $testMethod,
                    $request
                );
            }
        }
    }

    protected function getTestMethods(string $class)
    {
        $rc = new \ReflectionClass($class);
        $methods = [];
        foreach ($rc->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methods[$method->name] = $method;
        }

        if (count($methods) === 0) {
            return [];
        }

        $ret = [];
        foreach ($methods as $name => $method) {
            if (substr($name, 0, 4) === "test") {
                $partialName = ucfirst(substr($name, 4));
                $ret[] = [
                    $name,
                    $this->getRequestMethod($methods, $partialName),
                    $this->getProvideMethod($methods, $partialName),
                ];
            }
        }
        return $ret;
    }

    private function getRequestMethod(array $methods, string $partialName)
    {
        $name = "request$partialName";
        if (!isset($methods[$name])) {
            throw new \Exception("missing required method $name");
        }

        $method = $methods[$name];
        if (!$method->isPublic()) {
            throw new \Exception("$name must be public");
        }
        return $method;
    }

    private function getProvideMethod(array $methods, string $partialName)
    {
        $name = "provide$partialName";
        if (!isset($methods[$name])) {
            return null;
        }

        $method = $methods[$name];
        if (!$method->isPublic()) {
            throw new \Exception("$name must be public");
        }
        return $method;
    }
}
