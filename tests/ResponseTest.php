<?php

namespace sndsgd\http;


class ResponseTest extends \PHPUnit_Framework_TestCase
{
   protected static $responses = [];


   public static function setUpBeforeClass()
   {
      foreach (["google.com", "www.google.com"] as $domain) {
         $path = __DIR__."/example-responses/$domain";
         $json = file_get_contents($path);
         static::$responses[$domain] = json_decode($json, true);
      }
   }


   public function test()
   {
      list($info, $body) = static::$responses["google.com"];
      $res = new Response($info, $body);
      $this->assertEquals(301, $res->getStatusCode());
      $this->assertEquals("text/html", $res->getContentType());
      $this->assertEquals(0.060751, $res->getDuration());
      $this->assertEquals("http://www.google.com/", $res->getRedirectUrl());
      $this->assertEquals("gws", $res->getHeader("SeRvEr"));
      $this->assertTrue(is_string($res->getBody()));

   }
}
