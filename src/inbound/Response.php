<?php

namespace sndsgd\http\inbound;

use \sndsgd\http\HeaderTrait;


/**
 * Base class for inbound responses
 */
abstract class Response
{
   use HeaderTrait;

   /**
    * The response body
    *
    * @var string
    */
   protected $body;

   /**
    * @param string $body
    */
   public function setBody($body)
   {
      $this->body = $body;
   }

   /**
    * @return string
    */
   public function getBody()
   {
      return $this->body;
   }

   /**
    * Get the content type
    *
    * @return string|null
    */
   public function getContentType()
   {
      $ret = $this->getHeader("content-type");
      if ($ret === null) {
         return null;
      }
      $pos = strpos($ret, ";");
      return ($pos !== false) ? substr($ret, 0, $pos) : $ret;
   }
}
