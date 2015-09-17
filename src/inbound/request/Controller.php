<?php

namespace sndsgd\http\inbound\request;

use \sndsgd\http\inbound\Request;


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
   public abstract function execute();
}

