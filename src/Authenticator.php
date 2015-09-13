<?php

namespace sndsgd\http;

use \sndsgd\Storage;
use \sndsgd\user\model\User;
use \sndsgd\user\model\Session;


/**
 * A user authentication class
 */
class Authenticator
{
   /**
    * The classname of the user model to use
    *
    * @var string
    */
   protected $userClassname = APP_USER_MODEL;

   /**
    * The classname of the session model to use
    *
    * @var string
    */
   protected $sessionClassname = APP_SESSION_MODEL;

   /**
    * The current session instance
    * 
    * @var \sndsgd\user\model\Session
    */   
   protected $session;

   /**
    * The user instance
    * 
    * @var \sndsgd\user\model\User
    */
   protected $user;

   /**
    * Set the user classname
    *
    * @param string $classname
    */
   public function setUserClassname($classname)
   {
      $this->userClassname = $classname;
   }

   /**
    * Attempt to authenticate using basic authentication
    * uses the user's `username` and `apikey`
    *
    * @param string $username
    * @param string $apikey
    * @return boolean
    */
   public function useBasicAuth($username, $apikey)
   {
      # if only the password exists, assume its a session token
      if ($username === null && $apikey !== null) {
         return $this->loadFromSessionToken($apikey);
      }

      # otherwise load the user by username and compare the apikeys
      spl_autoload_call("DoctrineProxies\\__CG__\\sndsgd\\user\\model\\User");
      spl_autoload_call("DoctrineProxies\\__CG__\\sndsgd\\user\\model\\Role");
      return $this->loginWithApikey($username, $apikey);
   }

   /**
    * Use the session cookie to authenticate the request
    * 
    * @return 
    */
   public function useCookie()
   {
      return (
         array_key_exists(APP_SESSION_COOKIE_NAME, $_COOKIE) &&
         $this->loadFromSessionToken($_COOKIE[APP_SESSION_COOKIE_NAME])
      );
   }

   /**
    * @param string $token
    * @return boolean
    */
   public function loadFromSessionToken($token)
   {
      if (!$this->session) {
         $query = Storage::getInstance()->get("doctrine")->createQuery(
            "SELECT s FROM {$this->sessionClassname} s
            WHERE s.dateDeleted is NULL AND s.token = ?1"
         );
         $query->setParameter(1, $token);
         $this->session = $query->getOneOrNullResult();
      }
      return ($this->session !== null);
   }

   /**
    * Load a user 
    * 
    * @param string $username
    * @return boolean
    */
   private function loadUser($username)
   {
      $query = Storage::getInstance()->get("doctrine")->createQuery(
         "SELECT u FROM {$this->userClassname} u 
         JOIN sndsgd\\user\\model\\Role r WITH r = u.role
         JOIN sndsgd\\user\\model\\Status s WITH s = u.status
         WHERE u.dateDeleted is NULL AND u.username = ?1"
      );
      $query->setParameter(1, $username);
      $this->user = $query->getOneOrNullResult();
      return $this->user !== null;
   }

   /**
    * @return \sndsgd\user\model\Session
    */
   public function getSession()
   {
      return $this->session;
   }

   /**
    * @return \sndsgd\user\model\User
    */
   public function getUser()
   {
      if ($this->user !== null) {
         return $this->user;
      }
      else if ($this->session !== null) {
         return $this->session->getUser();
      }
      return null;
   }

   /**
    * Login using a username and apikey
    *
    * @param string $username
    * @param string $apikey
    * @return boolean
    */
   public function loginWithApikey($username, $apikey)
   {
      return (
         $this->loadUser($username) && 
         $this->user->getApikey() === $apikey
      );
   }

   /**
    * Login with a username and password
    *
    * @param string $username
    * @param string $password
    * @return boolean
    */
   public function loginWithPassword($username, $password)
   {
      return (
         $this->loadUser($username) && 
         $this->user->verifyPassword($password)
      );
   }

   /**
    * Create a new session for the authorized user
    *
    * @return \sndsgd\user\model\user\Session
    */
   public function createSession()
   {
      $this->session = new Session;
      $this->session->setUser($this->user);
      return $this->session;
   }

   /**
    * Set the cookie value for cookie based auth
    *
    * @param string $value The value for the auth cookie
    * @param integer $expiration The cookie expiration
    * @return boolean The result of the call to `setcookie()`
    */
   public function setCookie($value, $expiration = 0)
   {
      $name = APP_SESSION_COOKIE_NAME;
      $path = APP_SESSION_COOKIE_PATH;
      return setcookie($name, $value, $expiration, $path);
   }
}

