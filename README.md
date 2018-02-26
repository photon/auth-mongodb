# auth-mongodb


[![Build Status](https://travis-ci.org/photon/auth-mongodb.svg?branch=master)](https://travis-ci.org/photon/auth-mongodb)

MongoDB Backend for user, group, acl storage in photon

## Quick start


1) Add the module in your project

    composer require "photon/auth-mongodb:dev-master"

or for a specific version

    composer require "photon/auth-mongodb:^2.0"

2) Define a MongoDB connection in your project configuration

  Declare your MongoBD database

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

  The authentification module use a session to store user information

    'session_storage' => '\photon\session\storage\MongoDB',
    'session_cookie_path' => '/',
    'session_timeout' => 4 * 60 * 60,
    'session_mongodb' => array(
        'database' => 'default',
        'collection' => 'session',
    ),

4) Configure the authentification backend

  Configure the authentification backend to use this module

    'auth_backend' => '\photon\Auth\MongoDBBackend',

5) Create a user

  Create your first user to be able to login

    $user = new \photon\auth\MongoDBUser;
    $user->setLogin('jd@exemple.com');
    $user->setPassword('strong');
    $user->save();

6) Create a login view

  Add a login view in your app, the following code is the minimal one

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

  Declare the login view in your urls.

    array('regex' => '#^/login$#',
          'view' => array('\Dummy', 'dummy'),
          'name' => 'login_view')

7) Enjoy !

## Advanced usage

### Custom user class

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

Then, you must configure the MongoDB auth backend to use your class. Edit your photon configuration file to add :

    'auth_mongodb' => array(
        'user_class' => '\My\App\MyUser',
    )

### Protect your view with ACLs

The following view `dummy` is protected by precondition.
The class `MongoDBPrecondition` will load the ACL with name `adminPanel` and ensure the user can access to this view, otherwize a 403 will be generated.

    class Dummy
    {
      public $dummy_precond = array(
      '\photon\auth\MongoDBPrecondition::adminPanel'
      );
      public function dummy($request, $match)
      {
        return new \photon\http\response\NoContent;
      }
    }

  The ACL can be created with the following code

    $acl = new \photon\auth\MongoDBAcl;
    $acl->setName('adminPanel');
    $acl->addUser($user);
    $acl->save();

### Conditional rendering in templates

  You can use `MongoDBTemplateTag` in your template to test user ACL.

    {acl 'adminPanel'}
    Will be display only if the user have the adminPanel acl
    {/acl}

  The template must be declared in your configuration file

    'template_tags' => array(
        'acl' => '\photon\auth\MongoDBTemplateTag',
    )
