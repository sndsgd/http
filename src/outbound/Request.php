<?php

namespace sndsgd\http\outbound;

use \sndsgd\http\HeaderTrait;


/**
 * Base class for outbound requests
 */
abstract class Request
{
   use HeaderTrait;

   /**
    * The request url
    *
    * @var string
    */
   protected $url;

   /**
    * The request method
    *
    * @var string
    */
   protected $method = "GET";

   /**
    * @param string $url
    */
   public function setUrl($url)
   {
      $this->url = $url;
   }

   /**
    * @return string
    */
   public function getUrl()
   {
      return $this->url;
   }

   /**
    * @param string $method
    */
   public function setMethod($method)
   {
      $this->method = $method;
   }

   /**
    * @return string
    */
   public function getMethod()
   {
      return $this->method;
   }

   /**
    * Send the request and return an initialized response
    *
    * @param string $class A response classname as string
    * @return \sndsgd\http\inbound\Response
    */
   abstract public function getResponse($class = null);
}
