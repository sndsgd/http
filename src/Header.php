<?php

namespace sndsgd\http;

use \InvalidArgumentException;
use \sndsgd\Arr;


class Header
{
   /**
    * Convenience method for parsing a header
    * 
    * @param string $str The header to parse
    * @return sndsgd\http\Header
    */
   public static function parse($str)
   {
      $header = new Header;
      $header->parseString($str);
      return $header;
   }

   /**
    * The http prototcol
    *
    * @var string
    */
   protected $protocol;

   /**
    * The http status code
    *
    * @var integer
    */
   protected $statusCode;

   /**
    * The fields in the header
    *
    * @var array.<string,string>
    */
   protected $fields = [];

   /**
    * Parse a string
    *
    * @param string $header
    */
   public function parseString($header)
   {
      $parts = explode("\r\n", $header, 2);
      if (count($parts) !== 2) {
         throw new InvalidArgumentException(
            "invalid value provided for 'header'; expecting a string that ".
            "utilizes \\r\\n line breaks"
         );
      }

      list($info, $fields) = $parts;
      list($protocol, $code, $message) = preg_split('/\s+/', $info, 3);
      $this->protocol = $protocol;
      $this->statusCode = intval($code);

      $lines = explode("\r\n", trim($fields));
      foreach ($lines as $line) {
         list($key, $value) = explode(":", $line, 2);
         $key = strtolower($key);
         $value = trim($value);
         Arr::addvalue($this->fields, $key, $value);
      }
   }

   /**
    * Get the protocol
    *
    * @return string
    */
   public function getProtocol()
   {
      return $this->protocol;
   }

   /**
    * Get the status code
    *
    * @return integer
    */
   public function getStatusCode()
   {
      return $this->statusCode;
   }

   /**
    * Get the status text
    * 
    * @return string|null
    * @return string The status code
    * @return null The provided status code was not found
    */
   public function getStatusText()
   {
      return Code::getStatusText($this->statusCode);
   }

   /**
    * Get a field value
    * 
    * @param string $name The name of the header field to retrieve
    * @return string|array.<string>|null
    */
   public function getFieldValue($name)
   {
      $name = strtolower($name);
      return (array_key_exists($name, $this->fields)) 
         ? $this->fields[$name]
         : null;
   }
}

