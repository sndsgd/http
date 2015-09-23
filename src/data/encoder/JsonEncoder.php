<?php

namespace sndsgd\http\data\encoder;

use \InvalidArgumentException;


class JsonEncoder extends \sndsgd\http\data\EncoderAbstract
{
   /**
    * A bitmask of options to pass to json_encode
    *
    * @var integer
    */
   protected $options = 0;

   /**
    * The max nesting depth of the data
    *
    * @var integer
    */
   protected $depth = 512;

   /**
    * @param integer $options
    */
   public function setOptions($options)
   {
      $this->options = $options;
   }

   /**
    * @param integer $depth
    */
   public function setDepth($depth)
   {
      if (!is_int($depth) || $depth < 0) {
         throw new InvalidArgumentException(
            "invalid value provided for 'depth'; ".
            "expecting an integer that is greater than 0"
         );
      }
      $this->depth = $depth;
   }

   /**
    * {@inheritdoc}
    */
   public function encode()
   {
      $this->encodedData = json_encode($this->data, $this->options, $this->depth);
      if ($this->encodedData === false) {
         $this->error = json_last_error();
         $this->errorDetail = json_last_error_msg();
         return false;
      }
      return true;
   }
}
