<?php

namespace photon\auth;

/*
 * MongoDB storage for group
 */
class MongoDBGroup extends \photon\storage\mongodb\Obj
  implements \JsonSerializable
{
    use MongoDB\Name,
        MongoDB\Users,
        MongoDB\Id;

    const collectionName = 'groups';

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
        $this->name = 'Group unknown';
        $this->users = array();
    }

    public function jsonSerialize()
    {
      return array(
        'id' => (string) $this->getId(),
        'name' => $this->getName(),
        'users' => $this->getUsers(),
      );
    }
}
