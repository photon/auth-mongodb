<?php

namespace tests;

class TagTest extends \photon\test\TestCase
{
    public function setup()
    {
        parent::setup();

        $db = \photon\db\Connection::get('default');
        $db->drop();
    }

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

        $req = \photon\test\HTTP::baseRequest('GET', '/template');
        $req->user = $user; // Login
        list($req, $resp) = $dispatcher->dispatch($req);
        $this->assertEquals(200, $resp->status_code);
        $this->assertNotEquals(false, strstr($resp->content, 'admin'));
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

        $req = \photon\test\HTTP::baseRequest('GET', '/template');
        $req->user = $user; // Login
        list($req, $resp) = $dispatcher->dispatch($req);
        $this->assertEquals(200, $resp->status_code);
        $this->assertEquals(false, strstr($resp->content, 'admin'));
    }
}
