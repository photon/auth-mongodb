<?php

class AclTest extends \photon\test\TestCase
{
    public function testCreateAcl()
    {
        $acl = new \photon\auth\MongoDBAcl;
        $acl->setName('Open the door');
        $acl->save();
    }
}
