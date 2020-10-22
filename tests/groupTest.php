<?php

namespace tests;

class GroupTest extends \photon\test\TestCase
{
    public function setup()
    {
        parent::setup();

        $db = \photon\db\Connection::get('default');
        $db->drop();
    }

    public function testCreateGroup()
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

      // Start of test
        $group = new \photon\auth\MongoDBGroup;
        $group->setName('Linux users');
        $this->assertEquals('Linux users', $group->getName());

        $group->save();
        $this->assertNotEquals(null, $group->getId());

      // No one in the group
        $rc = $group->containsUser($user1);
        $this->assertEquals(false, $group->containsUser($user1));
        $this->assertEquals(false, $group->containsUser($user2));

      // Add user1 in the group
        $group->addUser($user1);
        $this->assertEquals(true, $group->containsUser($user1));
        $this->assertEquals(false, $group->containsUser($user2));

      // Remove user1
        $group->removeUser($user1);
        $this->assertEquals(false, $group->containsUser($user1));
        $this->assertEquals(false, $group->containsUser($user2));

      // Add user2 in the group
        $group->addUser($user2);
        $this->assertEquals(false, $group->containsUser($user1));
        $this->assertEquals(true, $group->containsUser($user2));
    }

    public function testJsonEncodeGroup()
    {
      $group = new \photon\auth\MongoDBGroup;
      $group->setName('Linux users');
      $group->save();

      $json = json_encode($group);
      $this->assertNotEquals(false, $json);

      $info = json_decode($json, true);
      $this->assertArrayHasKey('id', $info);
      $this->assertArrayHasKey('name', $info);
      $this->assertEquals('Linux users', $info['name']);
      $this->assertArrayHasKey('users', $info);
    }
}
