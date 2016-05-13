<?php

namespace sndsgd\http\test;

abstract class AbstractTest
{
    protected $request;
    protected $response;

    /**
     * @param \sndsgd\http\outbound\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return \sndsgd\http\outbound\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \sndsgd\http\inbound\Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return \sndsgd\http\inbound\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
