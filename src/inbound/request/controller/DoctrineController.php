<?php

namespace sndsgd\api\inbound\request\controller;


class DoctrineController extends \sndsgd\http\inbound\request\Controller
{
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
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * The authenticator used to validate the user's credentials
     *
     * @var \sndsgd\http\Authenticator
     */
    protected $auth;

    /**
     * A form used to validate the request data
     *
     * @var \sndsgd\http\Form
     */
    protected $form;

    /**
     * If an entity has been loaded/created, it will be referenced here
     *
     * @var \sndsgd\model\ModelAbstract
     */
    protected $entity;

    /**
     * If an error response is generated, it will be referenced here
     *
     * @var \sndsgd\http\outbound\Response
     */
    protected $response = null;


    /**
     * Localize doctrine to cut down on Storage::getInstance()->get() calls
     */
    public function __construct()
    {
        $this->em = Storage::getInstance()->get("doctrine");
    }

    /**
     * 
     * @return \sndsgd\http\Authenticator
     */
    protected function getAuthenticator()
    {
        if ($this->auth === null) {
            $this->auth = new Authenticator;
            list($username, $apikey) = $this->request->getBasicAuth();
            $userFound = ($username !== null && $apikey !== null)
                ? $this->auth->useBasicAuth($username, $apikey)
                : $this->auth->useCookie();
        }
        return $this->auth;
    }

    /**
     * Attempts to authenticate a user for the request
     */
    public function authenticate()
    {
        return true;
        return (static::AUTHENTICATE === false);
            // $this->getUser() !== null
            // ($user = $this->getUser() || $this->userHasPermission())
    }

    /**
     * @return \sndsgd\user\model\User|null
     */
    public function getUser()
    {
        return $this->getAuthenticator()->getUser();
    }

    /**
     * @return \sndsgd\user\model\Session|null
     */
    public function getSession()
    {
        return $this->getAuthenticator()->getSession();
    }

    /**
     * Get response data for a successful request
     *
     * @return array<string,mixed>
     */
    protected function getSuccessResponseData()
    {
        return [
            "message" => $this->entity->getMsg(
                $this::SUCCESS_MESSAGE_VERB,
                $this::SUCCESS_MESSAGE_VERB_FIRST
            ),
            "payload" => $this->entity->toArray()
        ];
    }

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
     * Load the entity that corresponds with the request
     * Note: if this method fails, it *MUST* create a response on this object
     *
     * @return boolean
     */
    protected function loadEntity()
    {
        if ($this->entity !== null) {
            return $this->entity;
        }

        $property = $this->getEntityLoadProperty();
        $value = $this->getEntityLoadValue();

        $dql = "SELECT e FROM ".static::MODEL." e WHERE e.$property = ?1";
        $query = $this->em->createQuery($dql);
        $query->setParameter(1, $value);
        $this->entity = $query->getOneOrNullResult();
        if ($this->entity === null) {
            $this->response = $this->createResponse(404, [
                "message" => $this->getEntityMessage("not found"),
                "payload" => null
            ]);
            return false;
        }
        else if ($this->entity->isDeleted()) {
            $this->response = $this->createResponse(410, [
                "message" => $this->getEntityMessage("deleted"),
                "payload" => null
            ]);
            return false;
        }
        return true;
    }

    /**
     * @return \sndsgd\model\ModelAbstract
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Create and validate a form
     *
     * @param array|null $parameters
     * @return boolean
     */
    protected function validate(array $parameters)
    {
        $timer = Timer::create("validation");
        if (static::FORM !== "") {
            $this->createForm($parameters);
            if (!$this->form->validate()) {
                $this->response = $this->createValidationErrorResponse();
                $timer->stop();
                return false;
            }   
        }
        $timer->stop();
        return true;
    }

    /**
     * Create a form to validate the user input and populate it with data
     *
     * @param array<string,mixed> $data
     * @return void
     */
    protected function createForm(array $parameters)
    {
        $class = static::FORM;
        $this->form = new $class;
        $this->form->setRequest($this);
        $this->form->registerFields();
        $this->form->addValues($parameters);
    }

    /**
     *
     * @return array<string,mixed>
     */
    protected function getValidatedParameters()
    {
        return $this->form->exportValues();
    }

    /**
     * Save the entity to the database
     *
     * @return boolean
     */
    protected function saveEntity()
    {
        if ($this->entity->getUid() === null) {
            $this->em->persist($this->entity);
        }
        $this->em->flush();
        return true;
    }

    /**
     * Update the entity
     *
     * @param array<string,mixed> $values
     * @return boolean
     */
    protected function updateEntity(array $values)
    {
        foreach ($values as $name => $value) {
            $method = "set".ucfirst($name);
            $this->entity->$method($value);
        }
        return true;
    }

    /**
     * Get a description message for the current entity
     *
     * @param string $message The message to use with the entity description
     * @param boolean $plural Use the entity's plural description
     * @return string
     */
    public function getEntityMessage($message, $plural = false)
    {
        $class = static::MODEL;
        $desc = ($plural) ? $class::PLURAL : $class::SINGULAR;
        return "$desc $message";
    }

    /**
     * Get the current entity uid
     *
     * @param mixed $default A value to return if the entity does not exist
     * @return mixed
     */
    public function getEntityUid($default = null)
    {
        return ($this->entity === null) ? $default : $this->entity->getUid();
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
        $res = new Response;
        $res->setStatusCode($code);
        if ($data !== null) {
            $res->setData($data);
        }
        else if ($statusText = Code::getStatusText($code)) {
            $res->setData([
                "message" => $this->getEntityMessage(strtolower($statusText)),
                "payload" => null,
            ]);
        }
        return $res;
    }

    /**
     * Create a validation error response
     *
     * @return \sndsgd\api\outgoing\Response
     */
    protected function createValidationErrorResponse(array $errors = null)
    {
        $errors = ($errors) ?: $this->form->exportErrors();
        return $this->createResponse(400, [
            "message" => "Validation error",
            "errors" => $errors,
            "payload" => null,
        ]);
    }
}
