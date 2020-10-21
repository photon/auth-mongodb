<?php

namespace photon\auth;

use photon\http\response\RedirectToLogin;
use photon\http\Request as PhotonRequest;
use DateTime;

/*
 * MongoDB storage for user
 */
class MongoDBUser extends \photon\storage\mongodb\Obj
  implements \JsonSerializable
{
    use MongoDB\Name,
        MongoDB\Id;

    const collectionName = 'users';
    public $is_anonymous = false;

    public static function createIndex()
    {
      $config = MongoDBBackend::getConfig();

      $db = \photon\db\Connection::get();
      $collection = $db->selectCollection(self::collectionName);

      $collection->createIndex(
          array('name' => 1),
          array('background' => true)
      );

      $collection->createIndex(
          array($config['user_login'] => 1),
          array('unique' => true, 'background' => true)
      );
    }

    /**
     *  Set the user login
     */
    public function setLogin($login)
    {
        $config = MongoDBBackend::getConfig();
        $key = $config['user_login'];

        $this->{$key} = $login;
    }

    public function getLogin()
    {
        $config = MongoDBBackend::getConfig();
        $key = $config['user_login'];

        return $this->{$key};
    }

    /**
     *  Set the user password
     */
    public function setPassword($pwd)
    {
        if (is_string($pwd) === false) {
            throw new MongoDBException('Password is not a string');
        }

        if ($pwd === '') {
            throw new MongoDBException('Password is empty');
        }

        $this->password = password_hash($pwd, PASSWORD_DEFAULT);
    }

    /**
     *  Cleanup the user password, he will not be able to login anymore
     */
    public function clearPassword()
    {
        $this->password = null;
    }

    /**
     *  Verify the user password
     *
     * @return bool true if the password match, false otherwize
     */
    public function verifyPassword($pwd)
    {
        if (isset($this->password) === false) {
            return false;
        }

        if ($this->password === null) {
            return false;
        }

        $valid = password_verify($pwd, $this->password);
        if ($valid === false) {
            return false;
        }

        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
            $this->setPassword($pwd);
        }

        return true;
    }

    /**
     *  Block the user account (disable login)
     */
    public function block()
    {
        $this->block = true;
        $this->blockedSince = new \MongoDB\BSON\UTCDateTime((new DateTime)->getTimestamp() * 1000);
    }

    /**
     *  Unblock the user account (enable login)
     */
    public function unblock()
    {
        $this->block = false;
        $this->blockedSince = null;
    }

    /**
     *  Test if an account is blocked
     */
    public function isBlocked()
    {
        if (isset($this->block)) {
            return $this->block;
        }

        return false;
    }

    /**
     *  Get the date when the user have been blocked
     */
    public function getBlockedSince($convert2iso=false)
    {
        if (isset($this->blockedSince) && $this->blockedSince !== null) {
            if ($convert2iso) {
              return $this->blockedSince->toDateTime()->format('c');
            }
            return $this->blockedSince->toDateTime();
        }

        return null;
    }

    /**
     *  Set an expiration date on the account
     */
    public function setExpirationDate(DateTime $limit)
    {
        $this->expiration = new \MongoDB\BSON\UTCDateTime($limit->getTimestamp() * 1000);
    }

    /**
     *  Remove the expiration date on the account
     */
    public function clearExpirationDate()
    {
        $this->expiration = null;
    }

    /**
     *  Get the current expiration date on the account
     */
    public function getExpirationDate($convert2iso=false)
    {
        if (isset($this->expiration) && $this->expiration !== null) {
          if ($convert2iso) {
            return $this->expiration->toDateTime()->format('c');
          }
          return $this->expiration->toDateTime();
        }

        return null;
    }

    /**
     *  Test if an account have expired
     */
    public function isExpired()
    {
        $expiration = $this->getExpirationDate();
        if ($expiration === null) {
            return false;
        }

        $now = new DateTime("now");

        return ($now > $expiration);
    }

    /**
     *  Precondition to ensure the user is connected and use this user storage
     */
    public static function connected(PhotonRequest $request)
    {
        if (isset($request->user) === false) {
            return new RedirectToLogin($request);
        }

        if (is_a($request->user, __CLASS__) === false) {
            return new RedirectToLogin($request);
        }

        return true;
    }

    public function jsonSerialize()
    {
      return array(
        'id' => (string) $this->getId(),
        'name' => $this->getName(),
        'login' => $this->getLogin(),
        'blocked' => $this->isBlocked(),
        'blockedSince' => $this->getBlockedSince(true),
        'expired' => $this->isExpired(),
        'expiration' => $this->getExpirationDate(true),
      );
    }
}
