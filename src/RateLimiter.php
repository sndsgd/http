<?php

namespace sndsgd\http;

use \RuntimeException;
use \sndsgd\Storage;


/**
 * A rate limiter implemented using redis counters
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

    /**
     * @param string $uniqueId The unique cache key for the request
     */
    public function __construct(/*string*/ $uniqueId)
    {
        $this->cacheKey = __CLASS__."--$uniqueId";
        $this->redis = Storage::getInstance()->get("redis");
    }

    /**
     * @param integer $count
     */
    public function setCount(/*int*/ $count)
    {
        $this->count = $count;
    }

    /**
     * @return integer
     */
    public function getCount()/*: int */
    {
        return $this->count;
    }

    /**
     * @param integer $duration
     */
    public function setDuration(/*int*/ $duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return integer
     */
    public function getDuration()/*: int */
    {
        return $this->duration;
    }

    /**
     * Create or increment a counter for the current unique id
     * 
     * @return boolean If the user has requests remaining
     */
    public function increment()/*: bool */
    {
        $count = $this->redis->incr($this->cacheKey);
        if ($count === 1) {
            $this->redis->setTimeout($this->cacheKey, $this->duration);
        }
        $this->remainingRequests = $this->count - $count;
        return ($this->remainingRequests > 0);
    }

    /**
     * @return integer
     */
    public function getRemainingRequests()/*: array */
    {
        if ($this->remainingRequests === null) {
            throw new RuntimeException(
                "method prerequisite not called; ".
                "call `increment()` before `getRemainingRequests()`"
            );
        }
        return $this->remainingRequests;
    }

    /**
     * @return integer
     */
    public function getExpiration()/*: integer */
    {
        if ($this->expiration === null) {
            $this->expiration = $this->redis->ttl($this->cacheKey);
        }
        return $this->expiration;
    }

    /**
     * Get rate limit headers
     * 
     * @return array<string,integer>
     */
    public function getHeaders()/*: array */
    {
        if ($this->remainingRequests === null) {
            throw new RuntimeException(
                "method prerequisite not called; ".
                "call `increment()` before `getHeaders()`"
            );
        }

        return [
            'X-RateLimit-Expiration' => $this->getExpiration(),
            'X-RateLimit-Requests-Remaining' => $this->remainingRequests,
            'X-RateLimit-Duration' => $this->duration,
            'X-RateLimit-Quota' => $this->count,
        ];
    }
}
