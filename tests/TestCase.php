<?php

namespace tests;

use photon\config\Container as Conf;
use photon\auth\api\MongoDB;
use photon\auth\MongoDBAcl;
use photon\auth\MongoDBBackend;

abstract class TestCase extends \photon\test\TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        // Cleanup database
        $db = \photon\db\Connection::get('default');
        $db->drop();
    }
}
