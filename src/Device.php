<?php

namespace sndsgd\http;


/**
 * Determine information about a user from request headers
 */
class Device
{
    /**
     * The http headers
     *
     * @var array<string,array<string>>
     */
    protected $headers;

    /**
     * @param array<string,array<string>> $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }
}
