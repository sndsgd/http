<?php

namespace sndsgd\http\outbound\response;

use \sndsgd\ErrorTrait;
use \sndsgd\http\outbound\Response;


class WriterAbstract
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
            $protocol." ".
            $this->response->getStatusCode()." ".
            $this->response->getStatusText()
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
    abstract public function generateBody()/*: bool*/;

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