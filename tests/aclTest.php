<?php

class AclTest extends \photon\test\TestCase
{
  public function setup()
  {
    parent::setup();

    $db = \photon\db\Connection::get('default');
    $db->drop();
  }
  
  public function testCreateAcl()
  {
    // Users
    $user1 = new \photon\auth\MongoDBUser;
    $user1->setLogin('jd@exemple.com');
    $user1->setPassword('strong');
    $user1->save();

    $user2 = new \photon\auth\MongoDBUser;
    $user2->setLogin('jd2@exemple.com');
    $user2->setPassword('strong2');
    $user2->save();

    // Groups
    $group1 = new \photon\auth\MongoDBGroup;
    $group1->setName('Linux users');
    $group1->addUser($user1);
    $group1->save();

    $group2 = new \photon\auth\MongoDBGroup;
    $group2->setName('BSD users');
    $group2->save();

    // Start of test
    $acl = new \photon\auth\MongoDBAcl;
    $acl->setName('Open the door');
    $acl->save();

    // No user and no group
    $this->assertEquals(false, $acl->isAllow($user1));
    $this->assertEquals(false, $acl->isAllow($user2));

    // Add group1 in the acl
    $acl->addGroup($group1);
    $this->assertEquals(true, $acl->isAllow($user1));
    $this->assertEquals(false, $acl->isAllow($user2));
    $this->assertEquals(true, $acl->containsGroup($group1));
    $this->assertEquals(false, $acl->containsGroup($group2));

    // Remove group1
    $acl->removeGroup($group1);
    $this->assertEquals(false, $acl->isAllow($user1));
    $this->assertEquals(false, $acl->isAllow($user2));

    // Add group2 in the acl
    $acl->addGroup($group2);
    $this->assertEquals(false, $acl->isAllow($user1));
    $this->assertEquals(false, $acl->isAllow($user2));

    // Add user2 in group2
    $group2->addUser($user2);
    $group2->save();
    $this->assertEquals(false, $acl->isAllow($user1));
    $this->assertEquals(true, $acl->isAllow($user2));

    // Add user1 in ACL
    $acl->addUser($user1);
    $this->assertEquals(true, $acl->isAllow($user1));

  }
}
