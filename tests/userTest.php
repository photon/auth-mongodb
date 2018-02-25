<?php

class UserTest extends \photon\test\TestCase
{
  public function setup()
  {
    parent::setup();

    $db = \photon\db\Connection::get('default');
    $db->drop();
  }
  
  public function testCreateUser()
  {
    $user = new \photon\auth\MongoDBUser;

    $user->setLogin('jd@exemple.com');
    $this->assertEquals('jd@exemple.com', $user->getLogin());

    $user->setPassword('strong');
    $this->assertEquals(true, $user->verifyPassword('strong'));

    $user->save();
    $this->assertNotEquals(null, $user->getId());
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
    $this->setExpectedException('\photon\auth\MongoDBException');

    $user = new \photon\auth\MongoDBUser;
    $user->setPassword('');
  }

  public function testNotStringPassword()
  {
    $this->setExpectedException('\photon\auth\MongoDBException');

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
}
