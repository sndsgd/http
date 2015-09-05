<?php

namespace sndsgd\http\inbound;

use \Exception;


/**
 * Base class for inbound requests
 */
abstract class Request
{
   /**
    * Request body decoders
    *
    * @var array<string,string>
    */
   protected static $dataTypes = [
      "application/json" => "sndsgd\\http\\data\\decoder\\JsonDecoder",
      "multipart/form-data" => "sndsgd\\http\\data\\decoder\\MultipartDataDecoder",
      "application/x-www-form-urlencoded" => "sndsgd\\http\\data\\decoder\\UrlDecoder",
   ];

   /**
    * The request content type without a charset
    *
    * @var string
    */
   protected $contentType;

   /**
    * Basic auth details are stashed here
    * 
    * @var array<string|null>
    */
   protected $basicAuth;

   /**
    * Parameters included in the uri are stashed here after
    *
    * @var array<string,mixed>
    */
   protected $uriParameters;

   /**
    * Request query parameters are stashed here after they are decoded
    *
    * @var array<string,mixed>
    */
   protected $queryParameters;

   /**
    * Get the content type
    *
    * @return string|null
    */
   public function getContentType()
   {
      if ($this->contentType === null) {
         $contentType = $this->getHeader("content-type") ?: "";
         $pos = strpos($contentType, ";");
         $contentType = ($pos !== false) 
            ? substr($contentType, 0, $pos) 
            : $contentType;   
         $this->contentType = $contentType;         
      }
      return $this->contentType;
   }

   /**
    * Get the basic auth credentials
    *
    * @return array<string|null>
    */
   public function getBasicAuth()
   {
      if ($this->basicAuth === null) {
         $this->basicAuth = [
            array_key_exists("PHP_AUTH_USER", $_SERVER) 
               ? $_SERVER["PHP_AUTH_USER"] : null,
            array_key_exists("PHP_AUTH_PW", $_SERVER) 
               ? $_SERVER["PHP_AUTH_PW"] : null,
         ];
      }
      return $this->basicAuth;
   }

   /**
    * @param array<string,mixed> $params
    */
   public function setUriParameters(array $params)
   {
      $this->uriParameters = $params;
   }

   /**
    * @return array<string,mixed>
    */
   public function getUriParameters()
   {
      return $this->uriParameters;
   }

   /**
    * @return array<string,mixed>
    */
   public function getQueryParameters()
   {
      if ($this->queryParameters === null) {
         $result = [];
         $pos = strpos($_SERVER["REQUEST_URI"], "?");
         if ($pos !== false) {
            $queryString = substr($_SERVER["REQUEST_URI"], $pos + 1);
            $rfc = UrlEncodedParser::getRfc();
            $result = Url::decodeQueryString($queryString, $rfc);
         }
         $this->queryParameters = $result;
      }
      return $this->queryParameters;
   }

   /**
    * Get the request data using the content type
    * Note: only used for requests that contain a body
    *
    * @return array
    * @throws Exception If the provided content type is not acceptable
    */
   protected function getDecodedBody()
   {
      $contentType = $this->getContentType();
      if ($contentType === null) {
         return [];
      }

      if (!array_key_exists($contentType, static::$dataTypes)) {
         throw new Exception("Unknown Content-Type '$contentType'", 400);
      }

      $class = static::$dataTypes[$contentType];
      $decoder = new $class;
      return $decoder->getDecodedData();
   }
}