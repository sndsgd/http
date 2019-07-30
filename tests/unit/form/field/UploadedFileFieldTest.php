<?php

namespace sndsgd\form\field;

/**
 * @coversDefaultClass ::UploadedFileField
 */
class UploadedFileFieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider providerConstructor
     */
    public function testConstructor(string $name)
    {
        $ruleClass = \sndsgd\form\rule\UploadedFileRule::class;

        $field = new UploadedFileField($name);
        $this->assertSame($name, $field->getName());
        $rules = $field->getRules();
        $this->assertCount(1, $rules);
        $this->assertArrayHasKey($ruleClass, $rules);
        $this->assertInstanceOf($ruleClass, $rules[$ruleClass]);
    }

    public function providerConstructor()
    {
        return [
            ["test"],
            ["💩"],
        ];
    }

    public function testGetType()
    {
        $field = new UploadedFileField();
        $this->assertSame("file", $field->getType());
    }
}
