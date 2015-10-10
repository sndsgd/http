<?php

namespace sndsgd\http;


/**
 * A field collection for use with request validation
 */
abstract class Form extends \sndsgd\field\Collection
{
    /**
     * @var \sndsgd\http\incoming\Request
     */
    protected $request;

    /**
     * @param \sndsgd\http\incoming\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \sndsgd\http\incoming\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Function that adds the request specific fields to the field collection
     */
    abstract public function registerFields();
}
