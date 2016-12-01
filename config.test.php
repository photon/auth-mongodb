<?php

// Enable native support of PHP 64 bits integer
ini_set('mongo.native_long', 1);

return array(
    // Create a list of DB available
    'databases' => array(
        'default' => array(
            'engine' => '\photon\db\MongoDB',
            'server' => 'mongodb://localhost:27017/',
            'database' => 'auth',
            'options' => array(
                'connect' => true,
            ),
        ),
    ),

    // Session
    'session_storage' => '\photon\session\storage\MongoDB',
    'session_cookie_path' => '/',
    'session_timeout' => 4 * 60 * 60,
    'session_mongodb' => array(
        'database' => 'default',
        'collection' => 'session',
    ),

    // Auth
    'auth_backend' => '\Support\Auth\AuthMongoBackend',
);
