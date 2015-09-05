<?php

namespace sndsgd\http\outbound\request;

use \sndsgd\http\HeaderTrait;
use \sndsgd\Url;


/**
 * Host information for use across multiple requests
 */
class CurlHost extends \sndsgd\http\Host
{
   public function initRequest($method, $path, $data = null)
   {
      $url = $this->getUrl($path, false);
      $opts = $this->getOptions();

      $method = strtoupper($method);
      if ($method === "GET" || $method === "DELETE") {

      }

      $req = new CurlRequest;
      $req->setUrl();
      $req->setOptions($this->getOptions());

   }
}
