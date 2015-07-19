<?php

namespace sndsgd\http;


/**
 * @coversDefaultClass \sndsgd\http\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
   private function getTestRequest($method, $data)
   {
      $req = new Request("http://snds.gd/", $method);
      $req->setData($data);
      $req->setCurlOptions([
         CURLOPT_COOKIEFILE => __DIR__."/cookies",
         CURLOPT_COOKIEJAR => __DIR__."/cookies",
      ]);
      $req->setCurlOption(CURLOPT_SSL_VERIFYPEER, true);
      $req->setHeader("Content-Type", "application/json");
      $req->addHeader("X-TEST-HEADER", "one");
      $req->addHeader("X-TEST-HEADER", "two");
      return $req;
   }

   /**
    * @covers ::__construct
    * @covers ::getUrl
    * @covers ::getMethod
    */
   public function testConstructor()
   {
      $url = "http://snds.gd/";
      $method = "GET";
      $req = new Request($url);
      $this->assertEquals($url, $req->getUrl());
      $this->assertEquals($method, $req->getMethod());

      $method = "POST";
      $req = new Request($url, $method);
      $this->assertEquals($url, $req->getUrl());
      $this->assertEquals($method, $req->getMethod());
   }

   /**
    * @covers ::setCurlOption
    * @covers ::setCurlOptions
    * @covers ::getCurlOptions
    */
   public function testCurlOptions()
   {
      $req = $this->getTestRequest("get", ["query" => "test"]);
      $this->assertEquals([
         CURLOPT_COOKIEFILE => __DIR__."/cookies",
         CURLOPT_COOKIEJAR => __DIR__."/cookies",
         CURLOPT_SSL_VERIFYPEER => true,
         CURLOPT_HTTPHEADER => [
            "content-type: application/json",
            "x-test-header: one",
            "x-test-header: two"
         ]
      ], $req->getCurlOptions());
   }

   /**
    * @covers ::prepare
    * @covers ::hasUploadFiles
    */
   public function testPrepare()
   {
      $req = $this->getTestRequest("get", ["query" => "test"]);
      $req->prepare();

      $req = $this->getTestRequest("post", ["query" => "test"]);
      $req->prepare();

      $req = $this->getTestRequest("post", ["file" => "@/tmp/upload.txt"]);
      $req->prepare();
   }

   /**
    * @covers ::send
    * @covers ::__destruct
    */
   public function testSend()
   {
      $req = $this->getTestRequest("get", []);
      $result = $req->send();
      $this->assertTrue(is_array($result));

      list($info, $body) = $result;
      $this->assertTrue(is_array($info));
      $this->assertArrayHasKey("http_code", $info);
      $this->assertTrue(is_string($body));

   }
}

