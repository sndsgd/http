<?php

namespace sndsgd\http\test;

use \sndsgd\http\inbound\Response as InboundResponse;
use \sndsgd\http\outbound\Request as OutboundRequest;

class TestCase
{
    protected $request;
    protected $instance;
    protected $method;
    protected $parameters;

    public function __construct(
        AbstractTest $instance,
        string $method,
        \sndsgd\http\outbound\request\CurlRequest $request,
        array $parameters = []
    )
    {
        if (!method_exists($instance, $method)) {
            throw new InvalidArgumentException(
               "invalid value provided for 'method'; ".
               "expecting the name of a method that exists on ".get_class($instance)
            );
        }

        $this->request = $request;
        $this->instance = $instance;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function getRequest(): OutboundRequest
    {
        return $this->request;
    }

    /**
     * Call the test method
     *
     * @param \sndsgd\http\inbound\Response $response
     * @param array $parameters The parameters used to create the request
     * @return bool
     * @throws \Exception If the test fails
     */
    public function execute(InboundResponse $response)
    {
        $url = $this->request->getUrl();
        $duration = number_format($response->getDuration(), 3);
        $status = $response->getStatusCode();

        $line = "$url [$status] $duration";

        $this->instance->setRequest($response->getRequest());
        $this->instance->setResponse($response);

        try {
            call_user_func_array([$this->instance, $this->method], $this->parameters);
            echo "[  OK  ] - $line\n";
        }
        catch (\Exception $ex) {
            echo "[ FAIL ] - $line\n";
        }
    }
}

