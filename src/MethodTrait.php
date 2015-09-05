<?php

namespace sndsgd\http;


trait MethodTrait
{
   /**
    * The http method 
    *
    * @var string
    */
   protected $method = "GET";

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
}

