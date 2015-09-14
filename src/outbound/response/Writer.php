<?php

namespace sndsgd\http\outbound\response;

use \sndsgd\http\outbound\Response;


class Writer
{
   /**
    * A response instance
    * 
    * @var \sndsgd\http\Response
    */
   protected $response;

   /**
    * @param \sndsgd\api\Response $response
    */
   public function __construct(Response $response)
   {
      $this->response = $response;
   }

   /**
    * Write the response to the client
    * 
    */
   public function write()
   {
      $proto = array_key_exists("SERVER_PROTOCOL", $_SERVER)
         ? $_SERVER["SERVER_PROTOCOL"]
         : "HTTP 1.1";

      header(
         $proto." ". // HTTP 1.1
         $this->response->getStatusCode()." ". // 200
         $this->response->getStatusText() // OK
      );
      foreach ($this->response->getHeaders() as $key => $value) {
         header("$key: $value");
      }
   }
}
