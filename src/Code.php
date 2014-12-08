<?php

namespace sndsgd\http;

use \InvalidArgumentException;


/**
 * A dictionary of http codes
 */
class Code
{
   /**
    * Status codes and their respective status codes
    * 
    * @var array.<integer,string>
    */
   private static $codes = [
      # success
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      # redirection
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      307 => 'Temporary Redirect',
      # client error
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Requested Range Not Satisfiable',
      417 => 'Expectation Failed',
      428 => 'Precondition Required',
      429 => 'Too Many Requests',
      431 => 'Request Header Fields Too Large',
      # server error
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version Not Supported',
      507 => 'Insufficient Storage',
      508 => 'Loop Detected',
      509 => 'Bandwidth Limit Exceeded',
      511 => 'Network Authentication Required',
   ];

   /**
    * Get the relevant message for a status code
    *
    * @param integer $code The status code
    * @return string|null
    * @return string The status code
    * @return null The provided status code was not found
    */
   public static function getStatusText($code)
   {
      return array_key_exists($code, self::$codes)
         ? static::$codes[$code]
         : null;
   }

   /**
    * Determine if a status code matches a pattern
    *
    * @param integer $code A status code to test
    * @param string|integer $match A combination of numbers and wildcards
    * @return boolean
    */
   public static function matches($code, $match)
   {
      if (is_string($match) && preg_match('/[0-9x]{3}/i', $match)) {
         $match = str_replace('x', '[0-9]', $match);
      }
      else if (!is_int($match) || $match < 100 || $match > 599) {
         throw new InvalidArgumentException(
            "invalid value provided for 'match'; ".
            "expecting a match target that contains only numbers and 'x'"
         );
      }

      return (preg_match('/^'.$match.'$/i', $code) === 1);
   }
}

