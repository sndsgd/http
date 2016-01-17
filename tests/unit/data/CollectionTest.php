<?php

namespace sndsgd\http\data;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    private function createCollection($maxVars = 10, $maxNesting = 10, array $values = null)
    {
        $collection = new Collection($maxVars, $maxNesting, $values);
        $rc = new \ReflectionClass($collection);
        $property = $rc->getProperty("values");
        $property->setAccessible(true);
        $property->setValue($collection, $values);
        return $collection;
    }

    /**
     * @dataProvider providerConstructor
     */
    public function testConstructor($values, $expect)
    {
        $collection = $this->createCollection(100, 100, $values);
        $rc = new \ReflectionClass($collection);
        $property = $rc->getProperty("values");
        $property->setAccessible(true);
        $this->assertSame($expect, $property->getValue($collection));
    }

    public function providerConstructor()
    {
        return [
            [
                ["1", "two" => "2", "3"],
                [
                    "0" => "1",
                    "two" => "2",
                    "1" => "3",
                ],
            ],
            [
                [
                    "one[][one]" => "1",
                    "two[][][]" => "2",
                    "three[1][2][3][]" => "3",
                ],
                [
                    "one" => [["one" => "1"]],
                    "two" => [[["2"]]],
                    "three" => [
                        "1" => [
                            "2" => [
                                "3" => [
                                    "3"
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerCount
     */
    public function testCount($values, $count)
    {
        $collection = $this->createCollection($count, 10, $values);
        $this->assertCount($count, $collection);
    }

    public function providerCount()
    {
        $ret = [];

        // indexed values
        $end = mt_rand(2, 1000);
        $ret[] = [range(1, $end), $end];

        // multiple values per key
        $ret[] = [
            [
                "one" => ["1", "2"],
                "two" => ["3", "4", "5"],
            ],
            5,
        ];

        return $ret;
    }

    /**
     * @dataProvider providerToArray
     */
    public function testGetValues($test)
    {
        $collection = $this->createCollection(100, 100, $test);
        $this->assertSame($test, $collection->getValues());
    }

    public function providerToArray()
    {
        return [
            [["one" => "1", "two" => "2"]],
            [["one" => ["1", "2", "3"], "two" => "2", "three" => ["a" => "a", "b" => "b"]]],
        ];
    }

    /**
     * @dataProvider providerAddValueException
     * @expectedException \Exception
     */
    public function testAddValueException($maxVars, $maxNesting, $params)
    {
        $collection = $this->createCollection($maxVars, $maxNesting);
        foreach ($params as $name => $value) {
            $collection->addValue($name, $value);
        }
    }

    public function providerAddValueException()
    {
        return [
            [1, 1, [1, 2]],
            [1, 1, ["one[two]" => 3]],
            [1, 2, ["one[two][three]" => 4]],
            [1, 3, ["one[two][three][four]" => 5]],
            [1, 4, ["one[two][three][four][five]" => 6]],
        ];
    }

    /**
     * @dataProvider providerAddValue
     */
    public function testAddValue($name, $value, $expect)
    {
        $collection = $this->createCollection(100, 100);
        $collection->addValue($name, $value);
        $this->assertSame($expect, $collection->getValues());
    }

    public function providerAddValue()
    {
        return [
            [
                "key",
                "value",
                ["key" => "value"],
            ],
            [
                "key[]",
                "value",
                ["key" => ["value"]],
            ],
            [
                "key[0]",
                "value",
                ["key" => ["value"]],
            ],
            [
                "key[1]",
                "value",
                ["key" => ["1" => "value"]],
            ],
            [
                "key[str]",
                "value",
                ["key" => ["str" => "value"]],
            ],
            [
                "key[0][][two][three]",
                "value",
                [
                    "key" => [
                        0 => [
                            0 => [
                                "two" => [
                                    "three" => "value",
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                "emoji",
                "💩",
                ["emoji" => "💩"],
            ],
            [
                "💩",
                "emoji",
                ["💩" => "emoji"],
            ],
            [
                "test[💩][💩💩]",
                "emoji",
                [
                    "test" => [
                        "💩" => [
                            "💩💩" => "emoji",
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerMultiAddValue
     */
    public function testMultiAddValue($key, $values, $expect)
    {
        $collection = $this->createCollection(100, 100);
        foreach ($values as $value) {
            $collection->addValue($key, $value);
        }
        $this->assertSame($expect, $collection->getValues());
    }

    public function providerMultiAddValue()
    {
        return [
            [
                "a[b]",
                ["1", "2", "3"],
                ["a" => ["b" => ["1", "2", "3"]]],
            ],
        ];
    }
}
