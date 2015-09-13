<?php

namespace sndsgd\http\outbound;

use \InvalidArgumentException;
use \sndsgd\http\Code;
use \sndsgd\http\HeaderTrait;


/**
 * Base class for outbound responses
 */
abstract class Response
{
   use HeaderTrait;

   /**
    * The name of the class that will be used to 'write' the response
    * Note: 
    *
    * @var string
    */
   protected $writerClassname = "sndsgd\\http\\outbound\\response\\Writer";

   /**
    * The http status code
    *
    * @var integer
    */
   protected $statusCode = 200;

   /**
    * The http status text
    *
    * @var string
    */
   protected $statusText = "OK";

   /**
    * @param integer $code An http status code
    * @see \sndsgd\http\Code.php
    */
   public function setStatusCode($code)
   {
      $this->statusCode = $code;
      $this->statusText = Code::getStatusText($code);
      if ($this->statusText === null) {
         throw new InvalidArgumentException("invalid HTTP status code '$code'");
      }
   }

   /**
    * @return integer
    */
   public function getStatusCode()
   {
      return $this->statusCode;
   }

   /**
    * @return string
    */
   public function getStatusText()
   {
      return $this->statusText;
   }

   /**
    * Create a writer to 'write' the response
    * 
    * @return \sndsgd\http\response\Writer
    */
   public function createWriter()
   {
      $classname = $this->writerClassname;
      return new $classname($this);
   }

   /**
    * Send the response to the client
    *
    * @return void
    */
   public function send()
   {
      header(
         $_SERVER["SERVER_PROTOCOL"]." ". // HTTP 1.1
         $this->statusCode." ". // 200
         $this->statusText // OK
      );
      $this->writeHeaders();
   }

   /**
    * Write the headers
    */
   protected function writeHeaders()
   {
      foreach ($this->headers as $header => $value) {
         if (is_array($value)) {
            $value = implode(", ", $value);
         }
         header("$header: $value");
      }
   }
}
