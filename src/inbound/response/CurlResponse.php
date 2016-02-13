<?php

namespace sndsgd\http\inbound\response;

/**
 * A response to a request made with an instance of CurlRequest
 */
class CurlResponse extends \sndsgd\http\inbound\Response
{
    /**
     * The result of a call to `curl_info()`
     *
     * @var array<string,mixed>
     */
    protected $curlInfo;

    /**
     * @param array<string,mixed> $info
     */
    public function setCurlInfo(array $info)
    {
        $this->curlInfo = $info;

        # if headers were included in the response
        # remove them from the body, and parse them into the header collection
        if (
            $info["header_size"] &&
            $header = trim(substr($this->body, 0, $this->curlInfo["header_size"]))
        ) {
            $this->body = substr($this->body, $this->curlInfo["header_size"]);
            $parser = new \sndsgd\http\HeaderParser();
            $parser->parse($header);
            $this->setHeaders($parser->getFields());
        }
        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function getCurlInfo()
    {
        return $this->curlInfo;
    }

    public function getDuration(int $type = self::DURATION_TOTAL): float
    {
        if ($type === self::DURATION_TOTAL) {
            return $this->curlInfo["total_time"];
        }

        $dnsLookup = $this->curlInfo["namelookup_time"];
        $connect = $this->curlInfo["connect_time"];
        $preTransfer = $this->curlInfo["pretransfer_time"];

        $time = 0.0;
        if ($type & self::DURATION_DNS_LOOKUP) {
            $time += $dnsLookup;
        }
        if ($type & self::DURATION_CONNECT) {
            $time += $connect;
        }
        if ($type & self::DURATION_WAIT) {
            $time += $preTransfer - $dnsLookup - $connect;
        }
        if ($type & self::DURATION_TRANSFER) {
            $time += $this->curlInfo["total_time"] - $preTransfer;
        }
        return $time;
    }
}
