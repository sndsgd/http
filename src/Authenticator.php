<?php

namespace sndsgd\http;

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
    * The current session instance
    * 
    * @var \sndsgd\user\model\user\Session
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
    * @return array|null
    */
   public function useBasicAuth(array $basicAuth)
   {
      list($username, $apikey) = $basicAuth;

      # if only the password exists, assume its a session token
      if ($username === "" && $apikey !== "") {
         return $this->loadUserFromSessionToken($apikey);
      }

      # otherwise load the user by username and compare the apikeys
      spl_autoload_call("DoctrineProxies\\__CG__\\sndsgd\\user\\model\\User");
      spl_autoload_call("DoctrineProxies\\__CG__\\sndsgd\\user\\model\\Role");
      return ($this->loginWithApikey($username, $apikey))
         ? $this->user->toArray()
         : null;
   }

   /**
    * Use the session cookie to authenticate the request
    * 
    * @return 
    */
   public function useCookie()
   {
      return (array_key_exists(APP_SESSION_COOKIE_NAME, $_COOKIE))
         ? $this->loadUserFromSessionToken($_COOKIE[APP_SESSION_COOKIE_NAME])
         : null;
   }

   /**
    * @param string $token
    * @return array
    */
   public function loadFromSessionToken($token)
   {
      $this->session = Session::readCache($token);
      if (!$this->session) {
         $doctrine = Container::get("doctrine");
         $query = $doctrine->createQuery(
            "SELECT e FROM sndsgd\user\\model\\user\\Session e WHERE
            e.deletedAt is NULL AND
            e.token = :token"
         );
         $query->setParameter("token", $token);
         $this->session = $query->getOneOrNullResult();
         if ($this->session !== null) {
            $this->session = $this->session->writeCache();
         }
      }
      return $this->session;
   }

   /**
    * Get the user from a session instance
    * Note: will return an array, as the doctrine objects cannot be cached
    * 
    * @param string $token
    * @return array
    */
   public function loadUserFromSessionToken($token)
   {
      $session = $this->loadFromSessionToken($token);
      return ($session === null) ? null : $session["user"];
   }

   /**
    * Load a user 
    * 
    * @param string $username
    * @return boolean
    */
   private function loadUser($username)
   {
      $doctrine = Container::get("doctrine");
      $query = $doctrine->createQuery(
         "SELECT u FROM {$this->userClassname} u WHERE
         u.dateDeleted is NULL AND
         u.username = ?1"
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
      return $this->user;
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

