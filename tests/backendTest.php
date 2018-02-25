<?php

class BackendTest extends \photon\test\TestCase
{
  public function setup()
  {
    parent::setup();

    $db = \photon\db\Connection::get('default');
    $db->drop();
  }

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


}