<?php

namespace sndsgd\http;

class NullRoute extends Route
{
    public function __construct()
    {
        parent::__construct("", "", 0);
    }
}
