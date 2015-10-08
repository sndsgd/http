<?php

namespace sndsgd\http\data\encoder;

use \ReflectionClass;


/**
 * @coversDefaultClass \sndsgd\http\data\encoder\JsonEncoder
 */
class JsonEncoderTest extends \PHPUnit_Framework_TestCase
{
    private static $testData = [
        "number" => 42,
        "float" => .42,
        "string" => "forty-two",
        "true" => true,
        "false" => false,
        "null" => null,
        "array" => [42, 0.42, "forty-two"],
        "object" => [
            "number" => 42,
            "float" => .42,
            "string" => "forty-two",
            "true" => true,
            "false" => false,
            "null" => null,
            "array" => [42, 0.42, "forty-two"],
            "object" => [
                "number" => 42,
                "float" => .42,
                "string" => "forty-two",
                "true" => true,
                "false" => false,
                "null" => null,
                "array" => [42, 0.42, "forty-two"],
                "object" => [
                    "number" => 42,
                    "float" => .42,
                    "string" => "forty-two",
                    "true" => true,
                    "false" => false,
                    "null" => null,

                ]  
            ]
        ]
    ];

    /**
     * @covers ::setOptions
     */
    public function testSetOptions()
    {
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        $encoder = new JsonEncoder;
        $encoder->setOptions($options);

        $rc = new ReflectionClass($encoder);
        $prop = $rc->getProperty("options");
        $prop->setAccessible(true);
        $this->assertEquals($options, $prop->getValue($encoder));
    }

    /**
     * @covers ::setDepth
     */
    public function testSetDepth()
    {
        $depth = 42;
        $encoder = new JsonEncoder;
        $encoder->setDepth($depth);

        $rc = new ReflectionClass($encoder);
        $prop = $rc->getProperty("depth");
        $prop->setAccessible(true);
        $this->assertEquals($depth, $prop->getValue($encoder));
    }

    /**
     * @covers ::setDepth
     * @expectedException InvalidArgumentException
     */
    public function testSetDepthException()
    {
        $encoder = new JsonEncoder;
        $encoder->setDepth("abc");
    }

    /**
     * @covers ::encode
     */
    public function testEncode()
    {
        $encoder = new JsonEncoder;
        $encoder->setData(static::$testData);
        $this->assertTrue($encoder->encode());

        $encoder = new JsonEncoder;
        $encoder->setData(static::$testData);
        $encoder->setDepth(1);
        $this->assertFalse($encoder->encode());
        $this->assertEquals(JSON_ERROR_DEPTH, $encoder->getError());
    }

}