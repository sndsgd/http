<?php

namespace sndsgd\http;

use \Exception;
use \sndsgd\http\Code as HttpCode;
use \sndsgd\http\HeaderParser;
use \sndsgd\http\HeaderTrait;
use \sndsgd\Url;


class Response
{
   use HeaderTrait;

   /**
    * Content types and their relevatn parsers
    *
    * @todo
    * @var array<string,string>
    */
   protected static $contentTypes = [
      "application/json" => "JsonParser",
      "multipart/form-data" => null,
      "application/x-www-form-urlencoded" => null,
   ];

   /**
    * the result of a call to `curl_getinfo()`
    *
    * @var array
    */
   protected $info;

   /**
    * @var \sndsgd\Url
    */
   protected $url;

   /**
    * The raw response body
    *
    * @var string
    */
   protected $body;

   /**
    * Once decoded, the response data will be stashed here
    *
    * @var array
    */
   protected $data;

   /**
    * @param array $info The result of `curl_getinfo()`
    * @param string $body The response body, including headers
    */
   public function __construct(array $info, $content)
   {
      $this->info = $info;
      $this->url = Url::createFromString($info["url"]);

      if (($header = trim(substr($content, 0, $info["header_size"])))) {
         $parser = new HeaderParser($header);
         $parser->parse($header);
         $this->setHeaders($parser->getFields());
      }
      $this->body = substr($content, $this->info["header_size"]);
   }

   /**
    * Get the url of the request that resulted in this response
    *
    * @param boolean $stringify Whether or not to return a string
    * @return \sndsgd\Url|string
    */
   public function getUrl($stringify = false)
   {
      return ($stringify) ? $this->url->__toString() : $this->url;
   }

   /**
    * Get the http status code
    *
    * @return integer
    */
   public function getStatusCode()
   {
      return $this->info["http_code"];
   }

   /**
    * Get the content type
    *
    * @return string|null
    */
   public function getContentType()
   {
      $ret = $this->info["content_type"];
      $pos = strpos($ret, ";");
      return ($pos !== false) ? substr($ret, 0, $pos) : $ret;
   }

   /**
    * Get the duration the of the request
    *
    * @param integer $precision The number of decimal places
    * @return float
    */
   public function getDuration($precision = -1)
   {
      $time = floatval($this->info["total_time"]);
      return ($precision === -1) ? $time : number_format($time, $precision);
   }

   /**
    * Get the redirect url url
    *
    * @return string|null
    */
   public function getRedirectUrl()
   {
      $url = $this->info["redirect_url"];
      return $url ? $url : null;
   }

   /**
    * Get the response body
    *
    * @return string
    */
   public function getBody()
   {
      return $this->body;
   }

   /**
    * @todo: create parsers for various content types
    *
    * @return array
    */
   public function getData()
   {
      if ($this->data === null) {
         $contentType = $this->getContentType();
         if (!array_key_exists($contentType, static::$contentTypes)) {
            throw new Exception(
               "response content type '$contentType' is not supported"
            );
         }
         $this->data = json_decode($this->body, true);
      }
      return $this->data;
   }
}

