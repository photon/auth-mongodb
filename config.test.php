<?php

return array(
    'debug' => true,
    'tmp_folder' => sys_get_temp_dir(),

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

    // URLs
    'urls' => array(
      array('regex' => '#^/$#',
            'view' => array('\tests\Views\Dummy', 'dummy'),
            'name' => 'acl_view'),

      array('regex' => '#^/template$#',
            'view' => array('\tests\Views\Dummy', 'template'),
            'name' => 'acl_view'),

      array('regex' => '#^/login$#',
            'view' => array('\Dummy', 'dummy'),
            'name' => 'login_view')
    ),

    // Template
    'template_folders' => array(
        __DIR__ .'/tests/templates',
    ),
    'template_tags' => array(
        'acl' => '\photon\auth\MongoDBTemplateTag',
    ),
);
