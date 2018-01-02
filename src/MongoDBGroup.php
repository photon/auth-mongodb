<?php

namespace photon\auth;

/*
 * MongoDB storage for group
 */
class MongoDBGroup extends \photon\storage\mongodb\Object
{
    use MongoDB\Name,
        MongoDB\Users;

    const collectionName = 'groups';

    protected function initObject()
    {
        $this->name = 'Group unknown';
        $this->users = array();
    }
}
