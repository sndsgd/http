<?php

namespace sndsgd\http;

class Route
{
    /**
     * The request method required to match the route
     *
     * @var string
     */
    protected $method;

    /**
     * The request path required to match the route
     *
     * @var string
     */
    protected $path;

    /**
     * The route priority
     * A higher number means the route will appear higher in the routes definition
     * 
     * @var int
     */
    protected $priority;

    public function __construct(string $method, string $path, int $priority = 1)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->priority = $priority;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
