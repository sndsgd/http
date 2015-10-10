<?php

namespace sndsgd\http\outbound\response\writer;


class TemplateWriter extends \sndsgd\http\outbound\response\Writer
{
    /**
     * {@inheritdoc}
     */
    public function write()
    {
        $body = $this->response->render();
        $this->writeHeaders([
            "Content-Length" => strlen($body)
        ]);
        echo $body;
     }
}
