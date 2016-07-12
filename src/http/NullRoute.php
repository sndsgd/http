<?php

namespace sndsgd\http;

class NullRoute extends Route
{
    public function __construct()
    {
        $this->method = "";
        $this->path = "";
        $this->priority = 0;
    }
}
