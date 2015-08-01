<?php

namespace sndsgd\http\inbound\response;


/**
 * A response to a request made with an instance of CurlRequest
 */
class CurlResponse extends \sndsgd\http\inbound\Response
{
   /**
    * The result of a call to `curl_info()`
    *
    * @var array<string,mixed>
    */
   protected $curlInfo;

   /**
    * @param array<string,mixed> $info
    */
   public function setCurlInfo($info)
   {
      $this->curlInfo = $info;

      # if headers were included in the response
      # remove them from the body, and parse them into the HeaderTrait
      if (
         $info["header_size"] &&
         $header = trim(substr($this->body, 0, $info["header_size"]))
      ) {
         $this->body = substr($this->body, $this->info["header_size"]);
         $parser = new HeaderParser($header);
         $parser->parse($header);
         $this->setHeaders($parser->getFields());
      }
   }

   /**
    * @return array<string,mixed>
    */
   public function getCurlInfo()
   {
      return $this->curlInfo;
   }
}
