<?php

namespace sndsgd\http;

use \InvalidArgumentException;
use \sndsgd\http\HeaderTrait;
use \sndsgd\Url;


class Host
{
   use HeaderTrait;

   /**
    * The base url for all requests
    *
    * @var \sndsgd\Url
    */
   protected $url;

   /**
    * Options to pass to request instances
    *
    * @var array<mixed,mixed>
    */
   protected $options = [];

   /**
    * @param string $url A url to extract scheme, host, port, and path from
    */
   public function setUrl($url)
   {
      $url = Url::createFromString($url);
      if (!$url->getScheme() || !$url->getHost()) {
         throw new InvalidArgumentException(
            "invalid value provided for 'url'; ".
            "expecting a url with a scheme and a hostname"
         );
      }
      $this->url = $url;
      return $this;
   }

   /**
    * Given a path, get a url relative to the base uri
    *
    * @param string $path
    * @param boolean $stringify
    * @return \sndsgd\Url|string
    */
   public function getUrl($path = null, $stringify = false)
   {
      $ret = clone $this->url;
      if ($path !== null) {
         $ret->setPath($path);
      }
      return ($stringify) ? (string) $ret : $ret;
   }

   /**
    * @param array<mixed,mixed> $options
    */
   public function setOptions(array $options)
   {
      $this->options = $options;
   }

   /**
    * @return array<mixed,mixed>
    */
   public function getOptions()
   {
      return $this->options;
   }
}

