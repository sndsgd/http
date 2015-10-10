<?php

namespace sndsgd\http\outbound\response;

use \sndsgd\ErrorTrait;
use \sndsgd\http\outbound\Response;


class Writer
{
    use ErrorTrait;

    /**
     * A response instance
     * 
     * @var \sndsgd\http\outbound\Response
     */
    protected $response;

    /**
     * @param \sndsgd\http\outbound\Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Write the response headers
     *
     * @param array<string,string> $additionalHeaders
     */
    protected function writeHeaders(array $additionalHeaders = null)
    {
        $protocol = array_key_exists("SERVER_PROTOCOL", $_SERVER)
            ? $_SERVER["SERVER_PROTOCOL"]
            : "HTTP 1.1";

        header(
            $protocol." ". // HTTP 1.1
            $this->response->getStatusCode()." ". // 200
            $this->response->getStatusText() // OK
        );
        foreach ($this->response->getHeaders() as $key => $value) {
            header("$key: $value");
        }
        if ($additionalHeaders !== null) {
            foreach ($additionalHeaders as $key => $value) {
                header("$key: $value");
            }
        }
    }

    /**
     * Generate the response body, and update the response accordingly
     *
     * @return boolean
     */
    abstract public function generate();

    /**
     * Write the response to the client
     * 
     */
    public function write()
    {
        if ($this->response->getBody() === null) {
            $this->generate();
        }

        $this->writeHeaders();

        if ($this->response->getRequest()->getMethod() !== "HEAD") {
            echo $this->response->getBody();
        }
        
    }
}
