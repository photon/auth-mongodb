<?php

namespace photon\auth;
use \photon\config\Container as Conf;

class MongoDBBackend
{
    private static $defaultConfig = array(
        'user_class' => '\photon\auth\MongoDBUser',
    );

    public static function loadUser($user_id)
    {
        if ($user_id === null) {
            return false;
        }

        try {
            $user_id = trim($user_id);
            $config = Conf::f('auth_mongodb', self::$defaultConfig);
            $class = $config['user_class'];
            $user = new $class(array('login' => $user_id));
        } catch(\Exception $e) {
            return false;
        }

        return $user;
    }

    public static function authenticate($auth)
    {
        $user = self::loadUser($auth['login']);
        if (false === $user) {
            return false;
        }

        if ($user->verifyPassword($auth['password']) === false) {
            return false;
        }

        return $user;
    }
}

