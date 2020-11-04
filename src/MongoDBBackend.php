<?php

namespace photon\auth;

use \photon\config\Container as Conf;

class MongoDBBackend
{
    private static $defaultConfig = array(
        'acl_class'           => MongoDBAcl::class,
        'group_class'         => MongoDBGroup::class,
        'user_class'          => MongoDBUser::class,
        'token_class'         => MongoDBUserToken::class,
        'precondition_class'  => MongoDBPrecondition::class,
        'user_id'             => '_id',
        'user_login'          => 'login',
        'user_password'       => 'password',
        'admin_precondition'  => 'admin-users',
    );

    public static function getConfig()
    {
        $config = Conf::f('auth_mongodb', self::$defaultConfig);
        $config = array_merge(self::$defaultConfig, $config);

        return $config;
    }

    public static function createIndex()
    {
        $config = self::getConfig();

        $config['acl_class']::createIndex();
        $config['group_class']::createIndex();
        $config['user_class']::createIndex();
    }

    /**
     *  Load the user from the database
     *
     * @param $login The unique user id
     *
     * @return object The user object,
     *         false if the user do not exists
     */
    public static function loadUser($login)
    {
        if ($login === null) {
            return false;
        }

        try {
            $login = trim($login);
            $config = self::getConfig();
            $class = $config['user_class'];
            $user = new $class(array($config['user_login'] => $login));
        } catch (\Exception $e) {
            return false;
        }

        return $user;
    }

    /**
     *  Authenticate an existing user
     *
     * @return object The user object,
     *         false if the user do not exists or the password is invalid
     */
    public static function authenticate($auth)
    {
        $config = self::getConfig();

        // Ensure login is provided
        $key = $config['user_login'];
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
