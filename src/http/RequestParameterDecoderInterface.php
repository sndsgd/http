<?php

namespace sndsgd\http;

interface RequestParameterDecoderInterface
{
    /**
     * Retrieve request query parameters
     *
     * @return array<string,mixed>
     */
    public function getQueryParameters(): array;

    /**
     * Retrieve request body parameters
     *
     * @return array
     */
    public function getBodyParameters(): array;
}
