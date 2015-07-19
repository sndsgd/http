<?php

namespace sndsgd\http;

use \ReflectionClass;


class TestHeaderTrait
{
   use HeaderTrait;
}

/**
 * @coversDefaultClass \sndsgd\http\HeaderTrait
 */
class HeaderTraitTest extends \PHPUnit_Framework_TestCase
{
   private static $testHeaders = [
      "one" => 1,
      "two" => "two",
      "three" => [1, 2, 3]
   ];

   private function getProtectedProperty($class, $property)
   {
      $rc = new ReflectionClass($class);
      $property = $rc->getProperty($property);
      $property->setAccessible(true);
      return $property;
   }

   public function setUp()
   {
      $this->test = new TestHeaderTrait;
      $rc = new ReflectionClass($this->test);
      $this->property = $rc->getProperty("headers");
      $this->property->setAccessible(true);

      $this->assertCount(0, $this->property->getValue($this->test));
   }

   /**
    * @covers ::setHeader
    */
   public function testSetHeader()
   {
      # add a single value
      $this->test->setHeader("test", 42);
      $value = $this->property->getValue($this->test);
      $this->assertCount(1, $value);
      $this->assertEquals(["test" => 42], $value);

      # replace the value
      $this->test->setHeader("test", 4242);
      $value = $this->property->getValue($this->test);
      $this->assertCount(1, $value);
      $this->assertEquals(["test" => 4242], $value);
   }

   /**
    * @covers ::setHeaders
    */
   public function testSetHeaders()
   {
      $this->test->setHeader("temp", "shant exist");
      $this->test->setHeaders(static::$testHeaders);
      $value = $this->property->getValue($this->test);
      $this->assertEquals(static::$testHeaders, $value);
   }

   /**
    * @covers ::addHeader
    */
   public function testAddHeader()
   {
      $this->test->addHeader("test", 1);
      $value = $this->property->getValue($this->test);
      $this->assertCount(1, $value);
      $this->assertEquals(["test" => 1], $value);

      $this->test->addHeader("test", 2);
      $value = $this->property->getValue($this->test);
      $this->assertCount(1, $value);
      $this->assertEquals(["test" => [1,2]], $value);      
   }

   /**
    * @covers ::addHeaders
    */
   public function testAddHeaders()
   {
      $this->test->addHeaders(static::$testHeaders);
      $value = $this->property->getValue($this->test);
      $this->assertCount(3, $value);
      $this->assertEquals(static::$testHeaders, $value);
   }

   /**
    * @covers ::getHeader
    */
   public function testGetHeader()
   {
      $this->test->addHeaders(static::$testHeaders);
      $this->assertEquals(1, $this->test->getHeader("one"));
      $this->assertEquals("two", $this->test->getHeader("two"));
      $this->assertTrue($this->test->getHeader("nope") === null);
   }

   /**
    * @covers ::getHeaders
    */
   public function testGetHeaders()
   {
      $this->test->addHeaders(static::$testHeaders);
      $this->assertEquals(static::$testHeaders, $this->test->getHeaders());
   }
}

