<?php

use sndsgd\http\Code;


class CodeTest extends PHPUnit_Framework_TestCase
{
   public function testGetStatusText()
   {
      $this->assertEquals('OK', Code::getStatusText(200));
      $this->assertEquals('Created', Code::getStatusText(201));

      $this->assertEquals('Moved Permanently', Code::getStatusText(301));
      $this->assertEquals('Found', Code::getStatusText(302));

      $this->assertEquals('Bad Request', Code::getStatusText(400));
      $this->assertEquals('Not Found', Code::getStatusText(404));

      $this->assertEquals('Internal Server Error', Code::getStatusText(500));
      $this->assertEquals('Bandwidth Limit Exceeded', Code::getStatusText(509));

      $this->assertNull(Code::getStatusText(600));
      $this->assertNull(Code::getStatusText(0));
   }
}

