<?php

namespace sndsgd\http\inbound\request\controller;

use \sndsgd\http\inbound\request\Controller;


class PatchController extends Controller
{
    /**
     * {@inheritdoc}
     */
    const SUCCESS_MESSAGE_VERB = "updated";

    /**
     * {@inheritdoc}
     */
    public function generateResponse()
    {
        $this->loadEntity();
        $this->validate($this->getParameters());
        $this->updateEntity($this->getValidatedParameters());
        $this->saveEntity();
        return $this->createResponse(200, $this->getSuccessResponseData());
    }

    /**
     * Retrieve parameters that should be validated, and later updated
     * 
     * @return array<string,mixed>
     */
    protected function getParameters()
    {
        return $this->getBodyParameters();
    }

    /**
     * For a patch request, only properties that are supplied are updated
     * Therefore, we must ensure that at least one property was provided
     *
     * {@inheritdoc}
     */
    protected function validate(array $parameters)
    {
        if (count($parameters) === 0) {
            throw new BadRequestException("No parameters found in request body");
        }

        return parent::validate($parameters);
    }

    /**
     * Remove any fields that values were not submitted for
     * @todo: this may become an issue when validation for a given field 
     *        requires the value from a field that is not provided
     *
     * @param array<string,mixed> $data
     * @return void
     */
    protected function createForm(array $data)
    {
        $class = static::FORM;
        $this->form = new $class;
        $this->form->setController($this);
        $this->form->registerFields();
        foreach ($this->form->getFields() as $name => $field) {
            if (!array_key_exists($name, $data)) {
                $this->form->removeField($name);
            }
        }
        $this->form->addValues($data);
    }
}
