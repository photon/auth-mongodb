<?php

namespace photon\auth;
use photon\http\response\RedirectToLogin;
use photon\http\Request as PhotonRequest;

/*
 * MongoDB storage for user
 */
class MongoDBUser extends \photon\storage\mongodb\Object
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
     *  Precondition to ensure the user is connected and use this user storage
     */
    static public function connected(PhotonRequest $request)
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
