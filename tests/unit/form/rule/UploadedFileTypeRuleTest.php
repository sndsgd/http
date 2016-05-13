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

    /**
     * @dataProvider providerGetErrorMessage
     */
    public function testGetErrorMessage($types, $errorMessage, $expect)
    {
        $rule = new UploadedFileTypeRule(...$types);
        if ($errorMessage) {
            $rule->setErrorMessage($errorMessage);
        }
        $this->assertSame($expect, $rule->getErrorMessage());
    }

    public function providerGetErrorMessage()
    {
        return [
            [
                ["image/jpeg"],
                "",
                "must be a file of the following type: 'image/jpeg'",
            ],
            [
                ["image/gif", "image/png"],
                "",
                "must be a file of the following types: 'image/gif', 'image/png'",
            ],
            [
                ["text/plain"],
                "custom message %s",
                "custom message 'text/plain'",
            ],
        ];
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($value, $expect)
    {
        $rule = new UploadedFileTypeRule();
        $this->assertSame($expect, $rule->validate($value));
    }

    private function getUploadedFileMock($isType)
    {
        $mock = $this->getMockBuilder(\sndsgd\http\UploadedFile::class)
            ->disableOriginalConstructor()
            ->setMethods(['isType'])
            ->getMock();

        $mock->method('isType')->willReturn($isType);
        return $mock;
    }

    public function providerValidate()
    {
        return [
            [123, false],
            [$this->getUploadedFileMock(false), false],
            [$this->getUploadedFileMock(true), true],
        ];
    }
}
