<?php

namespace sndsgd\http\outbound;

use \InvalidArgumentException;
use \sndsgd\http\Status;
use \sndsgd\http\inbound\Request;


/**
 * Base class for outbound responses
 */
class Response
{
    /**
     * A reference to the request this instance is a response to
     *
     * @var \sndsgd\http\inbound\Request
     */
    protected $request;

    /**
     * The http status code
     *
     * @var integer
     */
    protected $statusCode = 200;

    /**
     * The http status text
     *
     * @var string
     */
    protected $statusText = "OK";

    /**
     * Once the body has been generated it is stored here
     *
     * @var string
     */
    protected $body;

    /**
     * @param \sndsgd\http\inbound\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->headers = new \sndsgd\http\HeaderCollection();
    }

    /**
     * @return \sndsgd\http\inbound\Request
     */
    public function getRequest()/*: Request*/
    {
        return $this->request;
    }

    public function setHeader(string $key, string $value)
    {
        $this->headers->set($key, $value);
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
     * Set the status code and text
     *
     * @param integer $code The http status code
     * @see \sndsgd\http\Code
     */
    public function setStatus(int $code)
    {
        $this->statusText = Status::getText($code);
        if ($this->statusText === null) {
            throw new InvalidArgumentException(
                "invalid value provided for 'code'; ".
                "expecting a valid http status code as an integer"
            );
        }
        $this->statusCode = $code;
    }

    /**
     * @param integer $statusCode
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return integer
     */
    public function getStatusCode()/*: integer*/
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusText
     */
    public function setStatusText(/*string*/ $statusText)
    {
        $this->statusText = $statusText;
    }

    /**
     * @return string
     */
    public function getStatusText()/*: string */
    {
        return $this->statusText;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->setHeader("Content-Length", mb_strlen($body));
        $this->body = $body;
    }
    
    /**
     * @return string
     */
    public function getBody()
    {
       return $this->body;
    }

    public function send()
    {
        $protocol = $this->request->getProtocol();
        header("$protocol {$this->statusCode} {$this->statusText}");
        foreach ($this->headers->getStringifiedArray() as $header) {
            header("$header");
        }

        echo $this->body;
    }
}
