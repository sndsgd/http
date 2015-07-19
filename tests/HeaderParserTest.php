<?php

namespace sndsgd\http;


/**
 * @coversDefaultClass \sndsgd\http\HeaderParser
 */
class HeaderParserTest extends \PHPUnit_Framework_TestCase
{
   protected static $headers = [];

   /**
    * @coversNothing
    */
   public static function setUpBeforeClass()
   {
      self::addHeader("http://google.com", <<<HEADER
HTTP/1.1 301 Moved Permanently
Alternate-Protocol: 80:quic,p=0.01
Cache-Control: public, max-age=2592000
Content-Length: 219
Content-Type: text/html; charset=UTF-8
Date: Tue, 11 Nov 2014 19:05:32 GMT
Expires: Thu, 11 Dec 2014 19:05:32 GMT
Location: http://www.google.com/
Server: gws
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
HEADER
);

      self::addHeader("https://www.google.com", <<<HEADER
HTTP/1.1 200 OK
Alternate-Protocol: 443:quic,p=0.01
Cache-Control: private, max-age=0
Content-Type: text/html; charset=ISO-8859-1
Date: Tue, 11 Nov 2014 19:31:45 GMT
Expires: -1
P3P: CP="This is not a P3P policy! See http://www.google.com/support/accounts/bin/answer.py?hl=en&answer=151657 for more info."
Server: gws
Set-Cookie: PREF=ID=471601aaf6d05f78:FF=0:TM=1415734305:LM=1415734305:S=LMH3Xd8mevGWt0kG; expires=Thu, 10-Nov-2016 19:31:45 GMT; path=/; domain=.google.com
Set-Cookie: NID=asdasdasd; expires=Wed, 13-May-2015 19:31:45 GMT; path=/; domain=.google.com; HttpOnly
Transfer-Encoding: chunked
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
HEADER
);
   }

   /**
    * @coversNothing
    */
   public static function addHeader($name, $content)
   {
      self::$headers[$name] = str_replace("\n", "\r\n", $content);
   }

   /**
    * @coversNothing
    */
   public static function tearDownAfterClass()
   {
      self::$headers = null;
   }


   public function testParseHttpGoogle()
   {
      $parser = new HeaderParser;
      $parser->parse(self::$headers["http://google.com"]);
      $this->assertEquals("HTTP/1.1", $parser->getProtocol());
      $this->assertEquals(301, $parser->getStatusCode());
      $this->assertEquals("Moved Permanently", $parser->getStatusText());

      $fields = $parser->getFields();
      $this->assertTrue(is_array($fields));
   }

   public function testParseHttpsGoogle()
   {
      $parser = new HeaderParser;
      $parser->parse(self::$headers["https://www.google.com"]);
      $this->assertEquals("HTTP/1.1", $parser->getProtocol());
      $this->assertEquals(200, $parser->getStatusCode());
      $this->assertEquals("OK", $parser->getStatusText());

      $fields = $parser->getFields();
      $this->assertTrue(is_array($fields));
      $this->assertEquals("chunked", $fields["transfer-encoding"]);
   }

   /**
    * @covers ::parse
    * @expectedException InvalidArgumentException
    */
   public function testBadHeader()
   {
      $parser = new HeaderParser;
      $parser->parse("HTTP/1.1 200 OK\nContent-Length: 219\n");
   }
}

