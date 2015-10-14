<?php

namespace sndsgd\http\inbound\request\controller;

use \sndsgd\http\inbound\request\Controller;
use \sndsgd\http\inbound\request\controller\DoctrineControllerTrait;


class PostController extends Controller
{
    /**
     * {@inheritdoc}
     */
    const SUCCESS_MESSAGE_VERB = "created";

    /**
     * {@inheritdoc}
     */
    protected function generateResponse()
    {
        $this->validate($this->getParameters());
        $this->createEntity($this->getValidatedParameters());
        $this->saveEntity();
        return $this->createResponse(201, $this->getSuccessResponseData());
    }

    /**
     * @return array<string,mixed>
     */
    protected function getParameters()
    {
        return $this->getBodyParameters();
    }

    /**
     * Create an entity
     *
     * @param array<string,mixed> $values The values to set on the model
     */
    public function createEntity(array $values)
    {
        $class = static::MODEL;
        $this->entity = new $class;
        $values = array_filter($values, function($value) {
            return $value !== null;
        });
        return $this->updateEntity($values);
    }

    /**
     * Save the entity to the database
     * 
     */
    public function saveEntity()
    {
        $this->em->persist($this->entity);
        $this->em->flush();
        return true;
    }
}
