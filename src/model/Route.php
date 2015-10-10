<?php

namespace sndsgd\http\model;

use \InvalidArgumentException;
use \ReflectionClass;


/**
 * @Entity
 * @Table(name="routes")
 * @UniqueEntity("handler")
 */
class Route extends \sndsgd\model\ModelAbstract
{
    const SINGULAR = "Route";
    const PLURAL = "Routes";

    /**
     * Create a route instance from a classname
     *
     * @param string $classname
     * @return \sndsgd\http\model\Route
     * @throws ReflectionException
     */
    public static function createFromClassname($classname)
    {
        $rc = new ReflectionClass($classname);
        if (!$rc->isSubclassOf("sndsgd\\http\\inbound\\Request")) {
            throw new InvalidArgumentException(
                "invalid value provided for 'classname'; ".
                "expecting the name of a subclass of 'genome\\Request' as string"
            );
        }

        $route = new self;
        $route->setHandler($classname);
        return $route;
    }

    /**
     * The url path for the route
     *
     * @Column(type="string")
     */
    protected $path;

    /**
     * The http method
     *
     * @Column(type="string", length=10)
     */
    protected $method;

    /**
     * Whether or not the route requires the user to be authenticated
     *
     * @Column(type="boolean")
     */
    protected $requireAuth;

    /**
     * The handler classname
     *
     * @Column(type="string")
     */
    protected $handler;

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        $this->setMethod($handler::METHOD);
        $this->setPath($handler::PATH);
        $this->setRequireAuth($handler::ALLOWED_ROLES !== "");
    }

    /**
     * @return string $handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param boolean $requireAuth
     */
    public function setRequireAuth($requireAuth)
    {
        if (!is_bool($requireAuth)) {
            throw new InvalidArgumentException(
                "invalid value provided for 'requireAuth'; ".
                "expecting a boolean"
            );
        }
        $this->requireAuth = $requireAuth;
    }

    /**
     * @return boolean
     */
    public function getRequireAuth()
    {
        return $this->requireAuth;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            "path" => $this->path,
            "method" => $this->method,
            "handler" => $this->handler,
            "requireAuth" => $this->requireAuth,
        ]);
    }
}
