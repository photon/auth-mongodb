<?php

class GroupTest extends \photon\test\TestCase
{
    public function testCreateGroup()
    {
        $group = new \photon\auth\MongoDBGroup;
        $group->setName('Linux users');
        $group->save();
    }
}
