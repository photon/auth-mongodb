<?php

namespace tests;

use DateTime;

class BackendTest extends TestCase
{
    public function testUnknownUser()
    {
        $user = \photon\auth\MongoDBBackend::loadUser(null);
        $this->assertEquals(false, $user);

        $user = \photon\auth\MongoDBBackend::loadUser('a@a.com');
        $this->assertEquals(false, $user);
    }

    public function testLoadKnownUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        $user2 = \photon\auth\MongoDBBackend::loadUser('jd@exemple.com');
        $this->assertEquals($user->getId(), $user2->getId());
    }

    public function testAuthenticateKnownUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        // Nominal
        $user2 = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.com',
        'password' => 'strong'
        ));
        $this->assertEquals($user->getId(), $user2->getId());

        // Bad password
        $rc = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.com',
        'password' => 'strongeee'
        ));
        $this->assertEquals(false, $rc);

        // Unknown user
        $rc = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.come',
        'password' => 'strongeee'
        ));
        $this->assertEquals(false, $rc);

        // No password
        $rc = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.com',
        ));
        $this->assertEquals(false, $rc);

        // No login
        $rc = \photon\auth\MongoDBBackend::authenticate(array(
        'password' => 'strongeee'
        ));
        $this->assertEquals(false, $rc);
    }

    public function testBlockedUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        // Not blocked
        $user2 = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.com',
        'password' => 'strong'
        ));
        $this->assertEquals($user->getId(), $user2->getId());

        // Blocked
        $user->block();
        $user->save();
        $rc = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.com',
        'password' => 'strong'
        ));
        $this->assertEquals(false, $rc);
    }

    public function testExpiredUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        // Not blocked
        $user2 = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.com',
        'password' => 'strong'
        ));
        $this->assertEquals($user->getId(), $user2->getId());

        // Blocked
        $user->setExpirationDate(new DateTime('yesterday'));
        $user->save();
        $rc = \photon\auth\MongoDBBackend::authenticate(array(
        'login' => 'jd@exemple.com',
        'password' => 'strong'
        ));
        $this->assertEquals(false, $rc);
    }
}
