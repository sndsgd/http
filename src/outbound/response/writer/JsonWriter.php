<?php

namespace sndsgd\http\outbound\response\writer;

use \sndsgd\http\outbound\response\WriterAbstract;


class JsonWriter extends WriterAbstract
{
   /**
    * JSON encode options
    * 
    * @var integer
    */
   protected $encodeOptions = 0;

   /**
    * Set encode options to pass to json_encode
    * 
    * @param integer $options A bitmask of JSON_* constants
    */
   public function setEncodeOptions($options)
   {
      $this->encodeOptions = $options;
   }

   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $body = $this->response->getData();
      $body = (empty($body))
         ? "{}"
         : json_encode($body, $this->encodeOptions);

      $this->writeHeaders();
      header("Content-Type: application/json; charset=UTF-8");
      header("Content-Length: ".strlen($body));
      echo $body;
   }
}
