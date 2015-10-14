<?php

namespace sndsgd\http\inbound\request\controller;

use \sndsgd\http\inbound\request\Controller;


class GetController extends Controller
{
    /**
     * {@inheritdoc}
     */
    const SUCCESS_MESSAGE_VERB = "found";

    /**
     * {@inheritdoc}
     */
    protected function generateResponse()
    {
        $this->loadEntity();
        return $this->createResponse(200, $this->getSuccessResponseData());
    }
}
