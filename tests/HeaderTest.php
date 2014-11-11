<?php

use sndsgd\http\Header;


class HeaderTest extends PHPUnit_Framework_TestCase
{
   protected static $rawHeaders = [];

   public static function setUpBeforeClass()
   {
      $dir = __DIR__.DIRECTORY_SEPARATOR.'headers';
      $files = array_diff(scandir($dir), ['.', '..']);
      foreach ($files as $file) {
         $path = $dir.DIRECTORY_SEPARATOR.$file;
         $contents = file_get_contents($path);
         self::$rawHeaders[$file] = str_replace("\n", "\r\n", $contents);
      }
   }

   public static function tearDownAfterClass()
   {
      self::$rawHeaders = null;
   }

   public function testParseHttpGoogle()
   {
      $h = Header::parse(self::$rawHeaders['http-google.com']);
      $this->assertEquals('HTTP/1.1', $h->getProtocol());
      $this->assertEquals(301, $h->getStatusCode());
      $this->assertEquals('Moved Permanently', $h->getStatusText());
      $this->assertEquals(219, $h->getFieldValue('content-length'));
   }

   public function testParseHttpsGoogle()
   {
      $h = Header::parse(self::$rawHeaders['https-www.google.com']);
      $this->assertEquals('HTTP/1.1', $h->getProtocol());
      $this->assertEquals(200, $h->getStatusCode());
      $this->assertEquals('OK', $h->getStatusText());
      $this->assertNull($h->getFieldValue('content-length'));
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testBadHeader()
   {
      $h = Header::parse("HTTP/1.1 200 OK\nContent-Length: 219\n");
   }
}

