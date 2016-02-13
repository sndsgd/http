<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\HeaderCollection
 */
class HeaderCollectionTest extends \PHPUnit_Framework_TestCase
{
    private function getPropertyValue($class, $property)
    {
        $rc = new \ReflectionClass($class);
        $property = $rc->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($class);
    }

    /**
     * @dataProvider providerSetGet
     * @covers ::set
     * @covers ::get
     */
    public function testSetGet($hc, $key, $value, $getKey, $expect)
    {
        $hc->set($key, $value);
        $this->assertSame($expect, $hc->get($getKey));
    }

    public function providerSetGet()
    {
        return [
            [
                new HeaderCollection(),
                "Content-Type",
                "test",
                "CONTENT-TYPE",
                "test",
            ],
            [
                new HeaderCollection(),
                "Content-Type",
                "test",
                "CONTENT-TYPE",
                "test",
            ],

            # set will override any existing values
            [
                (new HeaderCollection())->set("Content-Type", "init"),
                "Content-Type",
                "test",
                "CONTENT-TYPE",
                "test",
            ],
        ];
    }

    /**
     * @dataProvider providerSetMultiple
     * @covers ::setMultiple
     */
    public function testSetMutiple($hc, array $headers, array $expect)
    {
        $hc->setMultiple($headers);
        $this->assertSame($expect, $this->getPropertyValue($hc, "headers"));
    }

    public function providerSetMultiple()
    {
        return [
            [
                new HeaderCollection(),
                [
                    "Test-Value" => "one",
                    "TEST-VALUE" => "two",
                ],
                [
                    "Test-Value" => "two",
                ],
            ],
            [
                (new HeaderCollection())->set("one", "first value"),
                [
                    "one" => "one",
                    "Second-Value" => "two",
                ],
                [
                    "one" => "one",
                    "Second-Value" => "two",
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerAdd
     * @covers ::add
     */
    public function testAdd($hc, $key, $value, $expect)
    {
        $hc->add($key, $value);
        $this->assertSame($expect, $this->getPropertyValue($hc, "headers"));
    }

    public function providerAdd()
    {
        return [
            [
                new HeaderCollection(),
                "value",
                "one",
                ["value" => "one"],
            ],
            [
                (new HeaderCollection())->set("value", "one"),
                "value",
                "two",
                ["value" => ["one", "two"]],
            ],
        ];
    }

    /**
     * @dataProvider providerAddMultiple
     * @covers ::addMultiple
     */
    public function testAddMultiple($hc, array $values, array $expect)
    {
        $hc->addMultiple($values);
        $this->assertSame($expect, $this->getPropertyValue($hc, "headers"));
    }

    public function providerAddMultiple()
    {
        return [
            [
                new HeaderCollection(),
                ["one" => "1", "two" => "2"],
                ["one" => "1", "two" => "2"],
            ],
        ];
    }

    /**
     * @dataProvider providerGet
     * @covers ::get
     */
    public function testGet($hc, $key, $expect)
    {
        $this->assertSame($expect, $hc->get($key));
    }

    public function providerGet()
    {
        $hc = new HeaderCollection();
        $hc->addMultiple([
            "First-Value" => "1",
            "SeCoNd-VaLuE" => "2",
            "THIRD-VALUE" => "3"
        ]);
        $hc->add("dupe", "1");
        $hc->add("dupe", "2");

        return [
            [$hc, "first-value", "1"],
            [$hc, "second-value", "2"],
            [$hc, "third-value", "3"],
            [$hc, "dupe", "1, 2"],
        ];
    }

    /**
     * @dataProvider providerGetMultiple
     * @covers ::getMultiple
     */
    public function testGetMultiple($hc, array $keys, array $expect)
    {
        $result = call_user_func_array([$hc, "getMultiple"], $keys);
        $this->assertSame($expect, $result);
    }

    public function providerGetMultiple()
    {
        $hc = new HeaderCollection();
        $hc->addMultiple([
            "First-Value" => "1",
            "SeCoNd-VaLuE" => "2",
            "THIRD-VALUE" => "3",
        ]);

        return [
            [$hc, ["first-value", "second-value"], ["1", "2"]],
        ];
    }

    /**
     * @dataProvider providerGetStringifiedArray
     * @covers ::getStringifiedArray
     */
    public function testGetStringifiedArray($hc, array $expect)
    {
        $this->assertSame($expect, $hc->getStringifiedArray());
    }

    public function providerGetStringifiedArray()
    {
        $hc = new HeaderCollection();
        $hc->add("dupe", "1");
        $hc->add("dupe", "2");
        $hc->addMultiple([
            "One" => "1",
            "VaLuE-TwO" => "2",
        ]);

        return [
            [
                $hc,
                [
                    "dupe: 1, 2",
                    "One: 1",
                    "VaLuE-TwO: 2",
                ],
            ],
        ];
    }

    public function test__toString($hc, $expect)
    {

    }

    // public function testStringifyHeaders()
    // {
    //     $this->test->addHeaders(static::$testHeaders);
    //     $res = $this->test->stringifyHeaders();
    //     $this->assertEquals([
    //         "one: 1",
    //         "two: two",
    //         "three: 1, 2, 3"
    //     ], $res);
    // }
}
