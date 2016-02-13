<?php

namespace sndsgd\http\inbound;

/**
 * Base class for inbound responses
 */
abstract class Response
{
    const DURATION_DNS_LOOKUP = 1;
    const DURATION_CONNECT = 2;
    const DURATION_WAIT = 4;
    const DURATION_TRANSFER = 8;
    const DURATION_TOTAL = 15;

    /**
     * The request this is a response for
     *
     * @var \sndsgd\http\outbound\Request
     */
    protected $request;

    /**
     * The response headers
     *
     * @var \sndsgd\http\HeaderCollection
     */
    protected $headers;

    /**
     * The response body
     *
     * @var string
     */
    protected $body;

    public function __construct(\sndsgd\http\outbound\Request $request)
    {
        $this->request = $request;
        $this->headers = new \sndsgd\http\HeaderCollection();
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setHeaders(array $headers)
    {
        # respect multiple set-cookie headers
        # manually add the headers as opposed to using `add|setMultiple`
        foreach ($headers as $key => $value) {
            $this->headers->add($key, $value);
        }
    }

    public function getHeader(string $key): string
    {
        return $this->headers->get($key);
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the content type
     *
     * @return string|null
     */
    public function getContentType()
    {
        $ret = $this->getHeader("content-type");
        if ($ret === null) {
            return null;
        }
        $pos = strpos($ret, ";");
        return ($pos !== false) ? substr($ret, 0, $pos) : $ret;
    }
}
