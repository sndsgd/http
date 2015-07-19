<?php

namespace sndsgd\http;

use \CurlFile;
use \Exception;
use \InvalidArgumentException;
use \sndsgd\data\Manager as DataTrait;
use \sndsgd\fs\File;
use \sndsgd\Mime;
use \sndsgd\Url;


/**
 * A request wrapper for cURL
 */
class Request
{
   use DataTrait, HeaderTrait;

   /**
    * The request url
    *
    * @var string
    */
   protected $url;

   /**
    * The request method
    *
    * @var string
    */
   protected $method = "GET";

   /**
    * A curl resource
    *
    * @var resource
    */
   protected $curl;

   /**
    * Custom cURL options
    *
    * @var array<integer,boolean|integer|string>
    */
   protected $curlOptions = [];

   /**
    * Constructor
    *
    * @param string $url The url to fetch
    * @param string $method The method to use
    */
   public function __construct($url, $method = "GET")
   {
      $this->url = $url;
      $this->method = strtoupper($method);
   }

   /**
    * Close the curl object if it is still open
    */
   public function __destruct()
   {
      if ($this->curl) {
         curl_close($this->curl);
      }
   }

   /**
    * Get the url
    *
    * @return string
    */
   public function getUrl()
   {
      return $this->url;
   }

   /**
    * Get the method
    *
    * @return string
    */
   public function getMethod()
   {
      return $this->method;
   }

   /**
    * Set a cURL option
    *
    * @param string $opt
    * @param mixed $value
    */
   public function setCurlOption($opt, $value)
   {
      $this->curlOptions[$opt] = $value;
   }

   /**
    * Set curl options for the request
    *
    * @param array<integer,boolean|integer|string> $opts
    */
   public function setCurlOptions(array $opts)
   {
      $this->curlOptions = $opts;
   }

   /**
    * Combine curl options for the request
    *
    * @param array $opts Base curl options, generally provided by the crawler
    * @return array
    */
   public function getCurlOptions(array $opts = [])
   {
      foreach ($this->curlOptions as $key => $value) {
         $opts[$key] = $value;
      }

      $headers = [];
      foreach ($this->headers as $key => $value) {
         if (is_array($value)) {
            foreach ($value as $val) {
               $headers[] = "$key: $val";
            }
         }
         else {
            $headers[] = "$key: $value";
         }
      }

      $opts[CURLOPT_HTTPHEADER] = $headers;
      return $opts;
   }

   /**
    * Create a cURL resource and set all the appropriate options
    *
    * @return resource
    */
   public function prepare()
   {
      $ch = curl_init();
      curl_setopt_array($ch, $this->getCurlOptions());
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_ENCODING, "");

      if ($this->method === "GET") {
         $queryParams = ($this->data)
            ? "?".Url::encodeQueryString($this->data)
            : "";
         curl_setopt($ch, CURLOPT_URL, $this->url.$queryParams);
      }
      else {
         curl_setopt($ch, CURLOPT_URL, $this->url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
         if ($this->hasUploadFiles()) {
            $this->setHeader("Expect", "");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
         }
         else {
            $this->setHeader("Content-Type", "application/json");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
         }
      }
      return $ch;
   }

   protected function hasUploadFiles()
   {
      $count = 0;
      $tmp = [];
      foreach ($this->data as $key => $value) {
         if (strpos($value, "@/") === 0) {
            $count++;
            $path = substr($value, 1);
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $mime = Mime::getTypeFromExtension($ext);
            $file = new CurlFile($path, $mime, basename($path));
            $tmp[$key] = $file;
         }
         else {
            $tmp[$key] = $value;
         }
      }

      $this->data = $tmp;
      return ($count > 0);
   }

   /**
    * Execute the curl request
    * 
    * @return array
    */
   public function send()
   {
      $this->curl = $this->prepare();
      $body = curl_exec($this->curl);
      $info = curl_getinfo($this->curl);
      return [$info, $body];
   }
}

