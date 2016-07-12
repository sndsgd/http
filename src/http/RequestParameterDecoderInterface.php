<?php

namespace sndsgd\http;

interface RequestParameterDecoderInterface
{
    /**
     * @return array<string,mixed>
     */
    public function getQueryParameters(): array;

    /**
     * Get the request data using the content type
     *
     * @return array
     */
    public function getBodyParameters(): array;
}
