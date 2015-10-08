<?php

namespace sndsgd\http;


/**
 * A field collection for use with request validation
 */
abstract class Form extends \sndsgd\field\Collection
{
    /**
     * @var \sndsgd\http\incoming\request\Controller
     */
    protected $controller;

    /**
     * @param \sndsgd\http\incoming\request\Controller $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return \sndsgd\http\incoming\request\Controller
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
