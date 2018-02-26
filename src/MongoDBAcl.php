<?php

namespace photon\auth;

use photon\auth\MongoDBUser;
use photon\auth\MongoDBGroup;

/*
 * MongoDB storage for ACL
 */
class MongoDBAcl extends \photon\storage\mongodb\Object
{
    use MongoDB\Name,
        MongoDB\Groups,
        MongoDB\Users,
        MongoDB\Id;

    const collectionName = 'acls';

    protected function initObject()
    {
        $this->name = 'ACL unknown';
        $this->users = array();
        $this->groups = array();
    }

    /*
     *  Search if a user is allow to use this ACL
     */
    public function isAllow(MongoDBUser $user)
    {
      // Search in user list
        if ($this->containsUser($user)) {
            return true;
        }

      // Search in group list
        foreach ($this->groups as $id) {
            $group = new MongoDBGroup(array('_id' => $id));
            if ($group->containsUser($user)) {
                return true;
            }
        }

        return false;
    }
}
