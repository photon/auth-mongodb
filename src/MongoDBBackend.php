<?php

namespace photon\auth;
use \photon\config\Container as Conf;

class MongoDBBackend
{
    private static $defaultConfig = array(
        'user_class'    => '\photon\auth\MongoDBUser',
        'user_id'       => 'login',
        'user_password' => 'password',
    );

    /**
     *  Load the user from the database
     *
     * @param $user_id The unique user id 
     */
    public static function loadUser($user_id)
    {
        if ($user_id === null) {
            return false;
        }

        try {
            $user_id = trim($user_id);
            $config = Conf::f('auth_mongodb', self::$defaultConfig);
            $class = $config['user_class'];
            $user = new $class(array($config['user_id'] => $user_id));
        } catch(\Exception $e) {
            return false;
        }

        return $user;
    }

    /**
     *  Authenticate a existing user
     *
     * @param $user_id The unique user id 
     * @return object The user object,
     *         false if the user do not exists or the password is invalid
     */
    public static function authenticate($auth)
    {
        $config = Conf::f('auth_mongodb', self::$defaultConfig);

        $user = self::loadUser($auth[$config['user_id']]);
        if (false === $user) {
            return false;
        }

        if ($user->verifyPassword($auth[$config['user_password']]) === false) {
            return false;
        }

        return $user;
    }
}

