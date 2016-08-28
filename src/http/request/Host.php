<?php

namespace sndsgd\http\request;

class Host implements HostInterface
{
    /**
     * The request instance
     *
     * @var \sndsgd\http\Request
     */
    protected $request;

    /**
     * A reference to the request environment instance
     *
     * @var \sndsgd\Environment
     */
    protected $environment;

    /**
     * @param \sndsgd\http\Request $request
     */
    public function __construct(\sndsgd\http\Request $request)
    {
        $this->request = $request;
        $this->environment = $request->getEnvironment();
    }

    /**
     * Retrieve the host ip address
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->environment["SERVER_ADDR"] ?? "";
    }

    /**
     * Retrieve the name of the request host
     *
     * @return string
     */
    public function getDnsName(): string
    {
        return $this->environment["SERVER_NAME"] ?? "";
    }
}
