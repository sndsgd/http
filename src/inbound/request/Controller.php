<?php

namespace sndsgd\http\inbound\request;

use \sndsgd\http\Code;
use \sndsgd\http\inbound\Request;
use \sndsgd\http\inbound\request\exception\BadRequestException;
use \sndsgd\api\outgoing\Response;


/**
 * Base request handler
 */
abstract class Controller
{
    /**
     * Subclasses *MUST* specify an HTTP method
     *
     * @var string
     */
    const METHOD = "";

    /**
     * Subclasses *MUST* specify a uri path
     *
     * @var string
     */
    const PATH = "";

    /**
     * Subclasses *MAY* not require authentication
     *
     * @var string
     */
    const AUTHENTICATE = true;

    /**
     * Subclasses *SHOULD* specify a model classname
     *
     * @var string
     */
    const MODEL = null;

    /**
     * Subclasses *MAY* specify a form classname
     *
     * @var string
     */
    const FORM = null;

    /**
     * Verb to describe the endpoint action in a success message
     * ex: created, updated, deleted, etc
     *
     * @var string
     */
    const SUCCESS_MESSAGE_VERB = "";

    /**
     * Whether or not to place the verb before the noun in response messages
     *
     * @var boolean
     */
    const SUCCESS_MESSAGE_VERB_FIRST = false;

    /**
     * Subclasses *MAY* force a request handler to ignore rate limiting
     *
     * @var string
     */
    const IGNORE_RATE_LIMIT = false;

    /**
     * Subclasses *MAY* set a higher priority so they can be matched sooner
     * The higher the number, the higher the priority
     *
     * @var integer
     */
    const ROUTER_PRIORITY = 1;

    /**
     * The inbound request object
     * 
     * @var \sndsgd\http\inbound\Request
     */
    protected $request;

    /**
     * An outbound response object
     *
     * @var \sndsgd\http\outbound\Response
     */
    protected $response;

    /**
     * An object used to determine the user who is making the request
     *
     * @var \sndsgd\http\Authenticator
     */
    protected $authenticator;

    /**
     * A form used to validate the request data
     *
     * @var \sndsgd\http\Form
     */
    protected $form;

    /**
     * @param \sndsgd\http\inbound\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the request and return a response
     *
     * @return \sndsgd\http\outbound\Response 
     */
    abstract public function execute();

    /**
     * Load the entity that corresponds with the request
     *
     * @return boolean
     * @throws \sndsgd\http\request\ExceptionAbstract
     */
    abstract protected function loadEntity();

    /**
     * Get the entity loaded by `loadEntity`
     *
     * @return \sndsgd\model\ModelAbstract
     */
    abstract protected function getEntity();

    /**
     * Update the entity
     * Note: this should *NOT* save the updated entity; use `saveEntity`
     *
     * @param array<string,mixed> $values
     * @throws \sndsgd\http\request\ExceptionAbstract
     */
    abstract protected function updateEntity(array $values);

    /**
     * Save the entity
     *
     * @return boolean
     * @throws \sndsgd\http\request\ExceptionAbstract
     */
    abstract protected function saveEntity();

    /**
     * Get the property to use when attempting to load the request entity
     * 
     * @return string
     */
    protected function getEntityLoadProperty()
    {
        return "uid";
    }

    /**
     * Get the unique value to use when attempting to load the request entity
     *
     * @return integer|string
     * @throws Exception
     */
    protected function getEntityLoadValue()
    {
        if (!array_key_exists("uid", $this->uriParameters)) {
            throw new Exception("entity uid does not exist in uri parameters");
        }
        return $this->uriParameters["uid"];
    }

    /**
     * Create and validate a form
     *
     * @param array|null $parameters
     * @return boolean
     */
    protected function validate(array $parameters)
    {
        if (static::FORM !== "") {
            $timer = Timer::create("validation");
            $this->createForm($parameters);
            $result = $this->form->validate();
            $timer->stop();

            if ($result !== true) {
                $ex = new BadRequestException("Validation Error");
                $ex->setValidationErrors($this->form->exportErrors());
                throw $ex;
            }
        }
        return true;
    }

    /**
     * Create a form to validate the user input and populate it with data
     *
     * @param array<string,mixed> $data
     */
    protected function createForm(array $parameters)
    {
        $class = static::FORM;
        $this->form = new $class;
        $this->form->setController($this);
        $this->form->registerFields();
        $this->form->addValues($parameters);
    }

    /**
     * Get the validated parameters from the form instance
     *
     * @return array<string,mixed>
     */
    protected function getValidatedParameters()
    {
        return $this->form->exportValues();
    }

    /**
     * Create a response
     *
     * @param integer $statusCode
     * @param array|null $data
     * @return \sndsgd\api\outgoing\Response
     */
    public function createResponse($code, array $data = null)
    {
        $response = new Response;
        $response->setStatusCode($code);
        if ($data !== null) {
            $response->setData($data);
        }
        else if ($statusText = Code::getStatusText($code)) {
            $response->setData([
                "message" => $this->getEntityMessage(strtolower($statusText)),
                "payload" => null,
            ]);
        }
        return $response;
    }
}
