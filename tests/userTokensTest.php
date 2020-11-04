<?php

namespace tests;

use DateTime;

class UserTokensTest extends TestCase
{
    public function testCreateToken()
    {
        $user = new \photon\auth\MongoDBUser;
        $user->setLogin('jd@exemple.com');
        $user->setPassword('strong');
        $user->save();

        $token = new \photon\auth\MongoDBUserToken;
        $token->setName('phpunit');
        $this->assertEquals('phpunit', $token->getName());
        $token->setUser($user);
        $token->save();

        $token->reload();
        $this->assertEquals('phpunit', $token->getName());
        $this->assertEquals(true, $token->isEnable());
        $this->assertEquals(0, $token->getAccessCount());
        $this->assertNotEmpty($token->getToken());

        $token->touch();
        $token->reload();
        $this->assertEquals(1, $token->getAccessCount());
    }
}
