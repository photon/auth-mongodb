<?php

namespace photon\auth;

use photon\http\response\RedirectToLogin;
use photon\http\Request as PhotonRequest;
use DateTime;

/*
 * MongoDB storage for user
 */
class MongoDBUser extends \photon\storage\mongodb\Obj
{
    use MongoDB\Name,
        MongoDB\Id;

    const collectionName = 'users';
    public $is_anonymous = false;

    /**
     *  Set the user login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
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
    }

    /**
     *  Unblock the user account (enable login)
     */
    public function unblock()
    {
        $this->block = false;
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
    public function getExpirationDate()
    {
        if (isset($this->expiration) && $this->expiration !== null) {
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
}
