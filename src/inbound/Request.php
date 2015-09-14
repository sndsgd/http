<?php

namespace sndsgd\http\inbound;

use \Exception;
use \sndsgd\http\data\decoder\UrlDecoder;
use \sndsgd\Url;


/**
 * Base class for inbound requests
 */
abstract class Request
{
   /**
    * Subclasses *MUST* specify an HTTP method
    *
    * @var string
    */
   const METHOD = "";

   /**
    * Subclasses *MUST* specify a uri path
    *
    * @var string
    */
   const PATH = "";

   /**
    * Subclasses *MAY* not require authentication
    *
    * @var string
    */
   const AUTHENTICATE = true;

   /**
    * Subclasses *MAY* force a request handler to ignore rate limiting
    *
    * @var string
    */
   const IGNORE_RATE_LIMIT = false;

   /**
    * Subclasses *MAY* set a higher priority so they can be matched sooner
    * The higher the number, the higher the priority
    *
    * @var integer
    */
   const ROUTER_PRIORITY = 1;

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
    * The uri path
    *
    * @var string
    */
   protected $path;

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
    * Request body parameters are stashed here after they are decoded
    *
    * @var array<string,mixed>
    */
   protected $bodyParameters;

   /**
    * In some cases a response will be generated, and then stashed here
    *
    * @var \sndsgd\http\outbound\Response
    */
   protected $response;

   public function getHeader($name, $default = "")
   {
      $name = strtoupper($name);
      $name = "HTTP_".preg_replace("~[^A-Z0-9]~", "_", $name);
      return (array_key_exists($name, $_SERVER)) ? $_SERVER[$name] : $default;
   }

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
    * @param string $path
    */
   public function setPath($path)
   {
      $this->path = $path;
   }

   /**
    * @return string
    */
   public function getPath()
   {
      return $this->path;
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
            $rfc = UrlDecoder::getRfc();
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
   protected function getBodyParameters()
   {
      if ($this->bodyParameters === null) {
         $contentType = $this->getContentType();
         if ($contentType === "") {
            return [];
         }

         if (!array_key_exists($contentType, static::$dataTypes)) {
            throw new Exception("Unknown Content-Type '$contentType'", 400);
         }

         $class = static::$dataTypes[$contentType];
         $decoder = new $class;
         $this->bodyParameters = $decoder->getDecodedData();
      }
      return $this->bodyParameters;
   }
}
