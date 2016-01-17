<?php

namespace sndsgd\http\outbound\response\writer;

use \sndsgd\http\outbound\response\WriterAbstract;


class JsonWriter extends WriterAbstract
{
    /**
     * JSON encode options
     * 
     * @var integer
     */
    protected $encodeOptions = 0;

    /**
     * Set encode options to pass to json_encode
     * 
     * @param integer $options A bitmask of JSON_* constants
     */
    public function setEncodeOptions($options)
    {
        $this->encodeOptions = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function generateBody()
    {
        $data = $this->response->getData();
        $body = (empty($data))
            ? "{}"
            : json_encode($data, $this->encodeOptions);

        if ($body === false) {
            $this->error = "failed to serialize body as JSON";
            $this->errorDetail = json_get_last_error_msg();
            return false;
        }

        $this->response->addHeader(
            "Content-Type" => "application/json; charset=UTF-8"
        );
        $this->response->setBody($body);
        return true;
    }
}
