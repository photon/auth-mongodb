<?php

namespace tests;

class PreconditionTest extends TestCase
{
    public function testUserIsAllow()
    {
        $dispatcher = new \photon\core\Dispatcher;

        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        $acl = new \photon\auth\MongoDBAcl;
        $acl->setName('adminPanel');
        $acl->addUser($user);
        $acl->save();

        $req = \photon\test\HTTP::baseRequest();
        $req->user = $user; // Login
        list($req, $resp) = $dispatcher->dispatch($req);
        $this->assertEquals(204, $resp->status_code);
    }

    public function testUserIsNotAllow()
    {
        $dispatcher = new \photon\core\Dispatcher;

        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        $acl = new \photon\auth\MongoDBAcl;
        $acl->setName('adminPanel');
        $acl->save();

        $req = \photon\test\HTTP::baseRequest();
        $req->user = $user; // Login
        list($req, $resp) = $dispatcher->dispatch($req);
        $this->assertEquals(403, $resp->status_code);
    }

    public function testUserIsNotConnected()
    {
        $dispatcher = new \photon\core\Dispatcher;

        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        $acl = new \photon\auth\MongoDBAcl;
        $acl->setName('adminPanel');
        $acl->addUser($user);
        $acl->save();

        $req = \photon\test\HTTP::baseRequest();
        list($req, $resp) = $dispatcher->dispatch($req);
        $this->assertEquals(403, $resp->status_code);
    }

    public function testUnknownACL()
    {
        $dispatcher = new \photon\core\Dispatcher;

        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        $req = \photon\test\HTTP::baseRequest();
        $req->user = $user; // Login
        list($req, $resp) = $dispatcher->dispatch($req);
        $this->assertEquals(403, $resp->status_code);
    }
}
