<?php

namespace sndsgd\http\data\decoder;

class DecoderOptionsTest extends \PHPUnit_Framework_TestCase
{
    use \phpmock\phpunit\PHPMock;

    /**
     * @dataProvider providerSimpleTests
     */
    public function testSimpleTests($method, $test, $expect)
    {
        $mock = $this->getFunctionMock(__NAMESPACE__, "ini_get");
        $mock->expects($this->any())->willReturn($test);

        $options = new DecoderOptions();
        $this->assertSame($expect, $options->$method());
    }

    public function providerSimpleTests()
    {
        return [
            ["getMaxVars", 123, 123],
            ["getMaxVars", 1001, 1001],
            ["getMaxNestingLevels", 123, 123],
            ["getPostDataReadingEnabled", true, true],
            ["getMaxFileCount", 123, 123],
        ];
    }

    /**
     * @dataProvider providerGetMaxFileSize
     */
    public function testGetMaxFileSize($test, $expect)
    {
        $mock = $this->getFunctionMock(__NAMESPACE__, "ini_get");
        $mock->expects($this->any())->willReturn($test);

        $options = new DecoderOptions();
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
