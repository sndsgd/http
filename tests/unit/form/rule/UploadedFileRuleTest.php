<?php

namespace sndsgd\form\rule;

class UploadedFileRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testGetDescription()
    {
        $rule = new \sndsgd\form\rule\UploadedFileRule();
        $this->assertSame("type:file", $rule->getDescription());
    }

    public function testGetErrorMessage()
    {
        $rule = new \sndsgd\form\rule\UploadedFileRule();
        $this->assertSame("must be a file", $rule->getErrorMessage());
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($value, $expect)
    {
        $rule = new \sndsgd\form\rule\UploadedFileRule();
        $this->assertSame($expect, $rule->validate($value));
    }

    public function providerValidate()
    {
        return [
            [new \sndsgd\http\UploadedFile("test.txt", "text/plain"), true],
            [new \stdClass(), false],
            [[], false],
        ];
    }
}
