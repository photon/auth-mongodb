<?php

namespace photon\auth;

/*
 * MongoDB storage for ACL
 */
class MongoDBAcl extends \photon\storage\mongodb\Object
{
    use MongoDB\Name,
        MongoDB\Groups,
        MongoDB\Users;

    const collectionName = 'acls';

    protected function initObject()
    {
        $this->name = 'ACL unknown';
        $this->users = array();
        $this->groups = array();
    }
}
