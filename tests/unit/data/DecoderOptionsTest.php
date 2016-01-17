<?php

namespace sndsgd\http\data;

class DecoderOptionsTest extends \PHPUnit_Framework_TestCase
{
    private function getMockWithReadValueStub($readValueResult)
    {
        $mock = $this->getMockBuilder("sndsgd\\http\\data\\DecoderOptions")
            ->setMethods(["readValue"])
            ->getMock();

        $mock->method("readValue")->willReturn($readValueResult);
        return $mock;
    }

    /**
     * @dataProvider providerReadValue
     */
    public function testReadValue($name, $expect)
    {
        $options = new DecoderOptions();
        $this->assertSame($expect, $options->readValue($name));
    }

    public function providerReadValue()
    {
        return [
            ["display_errors", ini_get("display_errors")],
        ];
    }

    /**
     * @dataProvider providerSimpleTests
     */
    public function testSimpleTests($method, $test, $expect)
    {
        $options = $this->getMockWithReadValueStub($test);
        $this->assertSame($expect, $options->$method());
    }

    public function providerSimpleTests()
    {
        return [
            ["getMaxVars", 123, 123],
            ["getMaxVars", 1001, 1001],
            ["getMaxNestingLevels", 123, 123],
            ["getMaxFileCount", 123, 123],
        ];
    }

    /**
     * @dataProvider providerGetMaxFileSize
     */
    public function testGetMaxFileSize($test, $expect)
    {
        $options = $this->getMockWithReadValueStub($test);
        $this->assertSame($expect, $options->getMaxFileSize());
    }

    public function providerGetMaxFileSize()
    {
        $sizes = [1, 10.1, 99.99];
        $ret = [];
        foreach ($sizes as $size) {
            $ret[] = ["{$size}B", intval($size)];
            $ret[] = ["{$size}K", intval($size * \sndsgd\Fs::BYTES_PER_KB)];
            $ret[] = ["{$size}M", intval($size * \sndsgd\Fs::BYTES_PER_MB)];
            $ret[] = ["{$size}G", intval($size * \sndsgd\Fs::BYTES_PER_GB)];
            $ret[] = ["{$size}T", intval($size * \sndsgd\Fs::BYTES_PER_TB)];
        }
        return $ret;
    }
}
