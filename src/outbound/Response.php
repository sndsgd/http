<?php

namespace sndsgd\http\outbound;

use \InvalidArgumentException;
use \sndsgd\http\Code;
use \sndsgd\DataTrait;
use \sndsgd\http\HeaderTrait;


/**
 * Base class for outbound responses
 */
class Response
{
    use DataTrait, HeaderTrait;

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
    }

    /**
     * @return \sndsgd\http\inbound\Request
     */
    public function getRequest()/*: Request*/
    {
        return $this->request;
    }

    /**
     * Set the status code and text
     *
     * @param integer $code The http status code
     * @see \sndsgd\http\Code
     */
    public function setStatus(/*integer*/ $code)
    {
        $this->statusText = Code::getStatusText($code);
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
    public function setStatusCode(/*integer*/ $statusCode)
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
        $this->addHeader("Content-Length", mb_strlen($body));
        $this->body = $body;
    }
    
    /**
     * @return string
     */
    public function getBody()
    {
       return $this->body;
    }
}
