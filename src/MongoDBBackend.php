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
            $config = array_merge(self::$defaultConfig, $config);
            $class = $config['user_class'];
            $user = new $class(array($config['user_id'] => $user_id));
        } catch (\Exception $e) {
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
        $config = array_merge($config, self::$defaultConfig);

        // Ensure login is provided
        $key = $config['user_id'];
        if (isset($auth[$key]) === false) {
            return false;
        }

        // Load user
        $user = self::loadUser($auth[$key]);
        if (false === $user) {
            return false;
        }

        // Ensure password is provided
        $key = $config['user_password'];
        if (isset($auth[$key]) === false) {
            return false;
        }

        // Verify password
        if ($user->verifyPassword($auth[$key]) === false) {
            return false;
        }

        // Ensure the user is not blocked
        if ($user->isBlocked()) {
            return false;
        }

        // Ensure the user is not expired
        if ($user->isExpired()) {
            return false;
        }
        
        return $user;
    }
}
