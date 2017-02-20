<?php

namespace sndsgd\http\request;

class Client implements ClientInterface
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
     * Once the ip address is determined, it'll be cached here
     *
     * @var string
     */
    protected $ip;

    /**
     * @param \sndsgd\http\Request $request
     */
    public function __construct(\sndsgd\http\Request $request)
    {
        $this->request = $request;
        $this->environment = $request->getEnvironment();
    }

    /**
     * Retrieve the client ip address
     *
     * @return string
     */
    public function getIp(): string
    {
        if ($this->ip === null) {
            foreach (["HTTP_X_FORWARDED_FOR", "X_FORWARDED_FOR"] as $key) {
                $proxyIpList = $this->environment[$key] ?? "";
                if ($proxyIpList) {
                    $commaPosition = strpos($proxyIpList, ",");
                    if ($commaPosition !== false) {
                        $this->ip = substr($proxyIpList, 0, $commaPosition);
                    } else {
                        $this->ip = $proxyIpList;
                    }
                    return $this->ip;
                }
            }
            $this->ip = $this->environment["REMOTE_ADDR"] ?? "";
        }
        return $this->ip;
    }
}
