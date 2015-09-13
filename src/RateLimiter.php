<?php

namespace sndsgd\http;

use \RuntimeException;
use \sndsgd\Storage;


/**
 * A rate limited implemented using redis counters
 */
class RateLimiter
{
   /**
    * The application redis instance
    *
    * @var \Redis
    */
   protected $redis;

   /**
    * The lookup key for the user / remote ip
    *
    * @var string
    */   
   protected $cacheKey;

   /**
    * The number of requests that can be made per quota period
    *
    * @var integer
    */
   protected $count = 7200;

   /**
    * The duration of the quota period in seconds
    *
    * @var integer
    */
   protected $duration = 3600;

   /**
    * The number of requests the user / remote ip has left in the quota period
    *
    * @var integer
    */
   protected $remainingRequests;

   /**
    * The number seconds left in the quota period
    *
    * @var integer
    */
   protected $expiration;


   public function __construct($uniqueId)
   {
      $this->cacheKey = __CLASS__."--$uniqueId";
      $this->redis = Storage::getInstance()->get("redis");
   }

   /**
    * @param integer $count
    */
   public function setCount($count)
   {
      $this->count = $count;
   }
   
   /**
    * @return integer
    */
   public function getCount()
   {
      return $this->count;
   }

   /**
    * @param integer $duration
    */
   public function setDuration($duration)
   {
      $this->duration = $duration;
   }
   
   /**
    * @return integer
    */
   public function getDuration()
   {
      return $this->duration;
   }

   public function increment()
   {
      $count = $this->redis->incr($this->cacheKey);
      if ($count === 1) {
         $this->redis->setTimeout($this->cacheKey, $this->duration);
      }
      $this->remainingRequests = $this->count - $count;
      $this->expiration = $this->redis->ttl($this->cacheKey);
   }

   /**
    * @return integer
    */
   public function getRemainingRequests()
   {
      return $this->remainingRequests;
   }

   /**
    * @return integer
    */
   public function getExpiration()
   {
      return $this->expiration;
   }
}

