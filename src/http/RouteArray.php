<?php

namespace sndsgd\http;

/**
 * A read only typed array consisting of instances of sndsgd\http\Route
 */
class RouteArray extends \sndsgd\ArrayAbstract
{
    /**
     * @param \sndsgd\http\Route ...$routes
     */
    public function __construct(Route ...$routes)
    {
        if (count($routes) < 1) {
            throw new \BadMethodCallException(
                "failed to create empty instance of ".__CLASS__
            );
        }
        parent::__construct($routes, true);
    }
}
