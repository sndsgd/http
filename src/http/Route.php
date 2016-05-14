<?php

namespace sndsgd\http;

class Route
{
    protected $method;
    protected $path;
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
