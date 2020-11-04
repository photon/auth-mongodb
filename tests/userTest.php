<?php

namespace tests;

use DateTime;
use photon\auth\MongoDBException;

class UserTest extends TestCase
{
    public function testCreateUser()
    {
        $user = new \photon\auth\MongoDBUser;

        $user->setLogin('jd@exemple.com');
        $this->assertEquals('jd@exemple.com', $user->getLogin());

        $user->setPassword('strong');
        $this->assertEquals(true, $user->verifyPassword('strong'));

        $user->save();
        $this->assertNotEquals(null, $user->getId());
        $this->assertEquals(false, $user->isBlocked());
        $this->assertEquals(false, $user->isExpired());
    }

    public function testVerifyPasswordUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        $this->assertEquals(false, $user->verifyPassword(null));
        $this->assertEquals(false, $user->verifyPassword(''));
        $this->assertEquals(false, $user->verifyPassword('BOB'));
        $this->assertEquals(false, $user->verifyPassword('stron'));
        $this->assertEquals(false, $user->verifyPassword('stronge'));
        $this->assertEquals(true, $user->verifyPassword('strong'));
    }

    public function testEmptyPassword()
    {
        $this->expectException(MongoDBException::class);

        $user = new \photon\auth\MongoDBUser;
        $user->setPassword('');
    }

    public function testNotStringPassword()
    {
        $this->expectException(MongoDBException::class);

        $user = new \photon\auth\MongoDBUser;
        $user->setPassword(12.3);
    }

    public function testViewPreconditionUserConnected()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

      // Nominal case
        $req = \photon\test\HTTP::baseRequest();
        $req->user = $user;
        $this->assertEquals(true, \photon\auth\MongoDBUser::connected($req));

      // user not uset in request
        $req = \photon\test\HTTP::baseRequest();
        $this->assertNotEquals(true, \photon\auth\MongoDBUser::connected($req));

      // user is not a MongoDBUser
        $req = \photon\test\HTTP::baseRequest();
        $req->user = 'JD';
        $this->assertNotEquals(true, \photon\auth\MongoDBUser::connected($req));
    }

    public function testExpiredUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();
        $this->assertEquals(false, $user->isExpired());

        $user->setExpirationDate(new DateTime('tomorrow'));
        $this->assertEquals(false, $user->isExpired());

        $user->setExpirationDate(new DateTime('yesterday'));
        $this->assertEquals(true, $user->isExpired());

        $user->clearExpirationDate();
        $this->assertEquals(false, $user->isExpired());
        $this->assertEquals(null, $user->getExpirationDate());
    }

    public function testBlockedUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();
        $this->assertEquals(false, $user->isBlocked());

        $user->block();
        $this->assertEquals(true, $user->isBlocked());

        $user->unblock();
        $this->assertEquals(false, $user->isBlocked());
    }
}
