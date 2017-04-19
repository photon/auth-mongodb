auth-mongodb
============

[![Build Status](https://travis-ci.org/photon/auth-mongodb.svg?branch=master)](https://travis-ci.org/photon/auth-mongodb)

MongoDB Backend for user storage in photon

Quick start
-----------

1) Add the module in your project

    composer require "photon/auth-mongodb:dev-master"

or for a specific version

    composer require "photon/auth-mongodb:2.0.0"

2) Define a MongoDB connection in your project configuration

    'databases' => array(
        'default' => array(
            'engine' => '\photon\db\MongoDB',
            'server' => 'mongodb://localhost:27017/',
            'database' => 'orm',
            'options' => array(
                'connect' => true,
            ),
        ),
    ),

3) Enable session backend

    'session_storage' => '\photon\session\storage\MongoDB',
    'session_cookie_path' => '/',
    'session_timeout' => 4 * 60 * 60,
    'session_mongodb' => array(
        'database' => 'default',
        'collection' => 'session',
    ),

4) Configure the auth backend

    'auth_backend' => '\photon\Auth\MongoDBBackend',

5) Create a user

    $user = new \photon\auth\MongoDBUser;
    $user->setLogin('jd@exemple.com');
    $user->setPassword('strong');
    $user->save();

6) Create a login view

    class MyViews {
	    public function login($request, $match)
	    {
            if ($request->method === 'POST') {
                $user = \photon\auth\Auth::authenticate($request->POST);
                if ($user !== false) {
                    \photon\auth\Auth::login($request, $user);
                    return new Redirect('/');
                }
            }

		    return shortcuts\Template::RenderToResponse('login.html', array(), $request);
        }
    }

7) Enjoy !

Advanced usage
--------------

If you want to add application specific content to the user class, you just have to extends it.
It's allow you to change the collection name where object are stored.

    class MyUser extends \photon\auth\MongoDBUser
    {
        const collectionName = 'foobarcollection';

        public function isAdmin()
        {
            return $this->admin;
        }
    }

Then, you must configure your user class in the configuration file

    'auth_mongodb' => array(
        'user_class' => '\My\App\MyUser',
    ),


