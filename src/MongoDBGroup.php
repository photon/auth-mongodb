<?php

namespace photon\auth;

/*
 * MongoDB storage for group
 */
class MongoDBGroup extends \photon\storage\mongodb\Obj
{
    use MongoDB\Name,
        MongoDB\Users,
        MongoDB\Id;

    const collectionName = 'groups';

    protected function initObject()
    {
        $this->name = 'Group unknown';
        $this->users = array();
    }
}
