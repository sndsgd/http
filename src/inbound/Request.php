<?php

namespace sndsgd\http\inbound;


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
   protected static $bodyDataTypes = [
      "application/json" => "sndsgd\\http\\data\\decoder\\JsonDecoder",
      "multipart/form-data" => "sndsgd\\http\\data\\decoder\\MultipartDataDecoder",
      "application/x-www-form-urlencoded" => "sndsgd\\http\\data\\decoder\\UrlDecoder",
      "text/html" => "sndsgd\\http\\data\\decoder\\HtmlDecoder"
   ];

   /**
    * Request query parameters are stashed here after the are decoded
    *
    * @var array<string,mixed>
    */
   protected $queryParameters;

   /**
    * Determine if the request is using basic authentication
    *
    * @return boolean
    */
   public function usesBasicAuth()
   {
      return (
         array_key_exists("PHP_AUTH_USER", $_SERVER) &&
         array_key_exists("PHP_AUTH_PW", $_SERVER)
      );
   }

   /**
    * Get the basic auth credentials
    *
    * @return array<string>
    */
   public function getBasicAuth()
   {
      return [
         $_SERVER["PHP_AUTH_USER"],
         $_SERVER["PHP_AUTH_PW"]
      ];
   }

   /**
    * Get the query parameters decoded as an array
    *
    * @return array<string,mixed>
    */
   protected function getQueryParameters()
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
   protected function getRequestData()
   {
      $contentType = $this->getContentType();
      if ($contentType === null) {
         return [];
      }

      if (!array_key_exists($contentType, static::$bodyDataTypes)) {
         throw new Exception("Unknown Content-Type '$contentType'", 400);
      }

      $class = static::$bodyDataTypes[$contentType];
      $decoder = new $class;
      return $decoder->getDecodedData();
   }
}
