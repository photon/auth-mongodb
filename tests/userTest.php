<?php

class UserTest extends \photon\test\TestCase
{
    public function testCreateUser()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();
    }
}
