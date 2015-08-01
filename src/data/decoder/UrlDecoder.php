<?php

namespace sndsgd\http\data\decoder;

use \Exception;
use \sndsgd\Arr;
use \sndsgd\Url;


class UrlDecoder extends \sndsgd\http\data\Decoder
{
   /**
    * A header key to specify the RFC the url was encoded for
    *
    * @var string
    */
   const HEADER_RFC_KEY = "HTTP_X_URL_RFC";

   /**
    * Get the RFC that should be used for encoding/decoding query strings
    *
    * @return integer
    */
   public static function getRfc()
   {
      $rfc = Url::RFC_1866;
      if (array_key_exists(self::HEADER_RFC_KEY, $_SERVER)) {
         $rfc = intval($_SERVER[self::HEADER_RFC_KEY]);
         $valid = [ Url::RFC_1866, Url::RFC_3986 ];
         if (!in_array($rfc, $valid)) {
            throw new Exception(
               "Unknown RFC provided for ".self::HEADER_RFC_KEY."; ".
               "expecting ".Arr::implode(",", $valid, " or ")
            );
         }
      }
      return $rfc;
   }

   /**
    * {@inheritdoc}
    */
   public function getDecodedData()
   {
      $contents = file_get_contents($this->path);
      return Url::decodeQueryString($contents, self::getRfc());
   }
}
