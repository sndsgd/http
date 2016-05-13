<?php

namespace sndsgd\form\rule;

class UploadedFileTypeRuleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDescription()
    {
        $rule = new UploadedFileTypeRule("image/jpeg", "image/png");
        $result = $rule->getDescription();
        $this->assertSame("file-type:image/jpeg,image/png", $result);
    }

    public function testGetErrorMessage()
    {
        $rule = new UploadedFileTypeRule("image/jpeg", "image/png");
        $expect = "must be a 'image/jpeg', or 'image/png' file";
        $result = $rule->getErrorMessage();
        $this->assertSame($expect, $result);
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
