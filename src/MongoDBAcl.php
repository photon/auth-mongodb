<?php

namespace photon\auth;

use photon\auth\MongoDBUser;
use photon\auth\MongoDBGroup;

/*
 * MongoDB storage for ACL
 */
class MongoDBAcl extends \photon\storage\mongodb\Obj
{
    use MongoDB\Name,
        MongoDB\Groups,
        MongoDB\Users,
        MongoDB\Id;

    const collectionName = 'acls';

    public static function createIndex()
    {
      $db = \photon\db\Connection::get();
      $collection = $db->selectCollection(self::collectionName);

      $collection->createIndex(
          array('name' => 1),
          array('unique' => true, 'background' => true)
      );
    }

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
        $config = MongoDBBackend::getConfig();
        $class = $config['group_class'];

        foreach ($this->groups as $id) {
            $group = new $class(array('_id' => $id));
            if ($group->containsUser($user)) {
                return true;
            }
        }

        return false;
    }

    /*
     *  Helper to batch create ACLs
     */
    public static function ensureExists(array $names)
    {
        $config = MongoDBBackend::getConfig();
        $class = $config['acl_class'];

        foreach ($names as $name) {
            try {
                $acl = new $class(array('name' => $name));
            } catch (\Exception $e) {
                $acl = new $class;
                $acl->setName($name);
                $acl->save();
            }
        }
    }
}
