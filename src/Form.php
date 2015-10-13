<?php

namespace sndsgd\http;

use \sndsgd\http\inbound\request\Controller;


/**
 * A field collection for use with request validation
 */
abstract class Form extends \sndsgd\field\Collection
{
    /**
     * @var \sndsgd\http\inbound\request\Controller
     */
    protected $controller;

    /**
     * @param \sndsgd\http\inbound\request\Controller $controller
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return \sndsgd\http\inbound\request\Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Function that adds the request specific fields to the field collection
     */
    abstract public function registerFields();
}
