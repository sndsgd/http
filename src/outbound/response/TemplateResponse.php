<?php

namespace sndsgd\http\inbound\request;



abstract class TemplateResponse extends \sndsgd\http\inbund\Request
{
   /**
    * @param array<string,string> $params
    */
   public function setParameters(array $params = [])
   {
      $this->parameters = $params;
   }

   /**
    * @param array<string,mixed> $user
    */
   public function setUser($user)
   {
      $this->user = $user;
   }

   /**
    * Handle the request, and return a response as string
    *
    * @return string
    */
   abstract public function handle();
}

