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


    public function __construct(\sndsgd\http\Request $request)
    {
        $this->request = $request;
        $this->environment = $request->getEnvironment();
    }

    public function getIp(): string
    {
        if ($this->ip === null) {
            $this->ip = $this->environment["REMOTE_ADDR"] ?? "";
            foreach (["HTTP_X_FORWARDED_FOR", "X_FORWARDED_FOR"] as $key) {
                if (!isset($this->environment[$key])) {
                    continue;
                }

                $this->ip = $this->environment[$key];
                if (strpos($this->ip, ",") !== false) {
                    list($this->ip) = preg_split("/,\s?/", $this->ip);
                }
                break;
            }
        }
        return $this->ip;
    }
}
