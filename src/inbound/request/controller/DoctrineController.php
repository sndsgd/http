<?php

namespace sndsgd\api\inbound\request\controller;


class DoctrineControllerTrait
{
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * If an entity has been loaded/created, it will be referenced here
     *
     * @var \sndsgd\model\ModelAbstract
     */
    protected $entity;

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
}
