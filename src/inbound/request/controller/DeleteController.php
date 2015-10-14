<?php

namespace sndsgd\http\inbound\request\controller;

use \Exception;
use \sndsgd\http\inbound\request\exception\InternalServerErrorException;
use \sndsgd\http\inbound\request\Controller;


abstract class DeleteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    const SUCCESS_MESSAGE_VERB = "deleted";

    /**
     * {@inheritdoc}
     */
    protected function generateResponse()
    {
        $this->loadEntity();
        $this->validate($this->getParameters());
        $this->deleteEntity();
        return $this->createResponse(200, $this->getSuccessResponseData());
    }

    /**
     * Retrieve parameters to use in processing the request
     * 
     * @return array<string,mixed>
     */
    protected function getParameters()
    {
        return array_merge(
            $this->getQueryParameters(),
            $this->getBodyParameters()
        );
    }

    /**
     * Delete the entity
     * Note: by default entities are just 'marked deleted'
     * Override this method whenever you need to really delete an entity
     * 
     * @return boolean
     * @throws Exception If the delete operation fails
     */
    protected function deleteEntity()
    {
        try {
            $this->entity->markDeleted();
            $this->saveEntity();
        }
        catch (Exception $ex) {
            $class = static::MODEL;
            $desc = $class::SINGULAR;
            throw new InternalServerErrorException("failed to delete $desc");
        }
    }
}
