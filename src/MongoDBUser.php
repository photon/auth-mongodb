<?php

namespace photon\auth;

class MongoDBUser extends \photon\storage\mongodb\Object
{
    const collectionName = 'users';
    public $is_anonymous = false;

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setPassword($pwd)
    {
        if (is_string($pwd) === false) {
            throw new \Exception('Password is not a string');
        }

        if ($pwd === '') {
            throw new \Exception('Password is empty');
        }

        $this->password = password_hash($pwd, PASSWORD_DEFAULT);
    }

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
}

