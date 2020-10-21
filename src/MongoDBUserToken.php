<?php

namespace photon\auth;

use photon\http\response\RedirectToLogin;
use photon\http\Request as PhotonRequest;
use DateTime;

/*
 * MongoDB storage for user tokens
 */
class MongoDBUserToken extends \photon\storage\mongodb\Obj
  implements \JsonSerializable
{
    use MongoDB\Name,
        MongoDB\Id;

    const collectionName = 'users_token';

    protected $mandatoryFields = array(
        'name',
        'token',
        'user',
        'ctm',
        'enable'
    );

    public static function createIndex()
    {
        $db = \photon\db\Connection::get();
        $collection = $db->selectCollection(self::collectionName);

        $collection->createIndex(
            array('token' => 1),
            array('unique' => true, 'background' => true)
        );

        $collection->createIndex(
            array('name' => 1),
            array('unique' => true, 'background' => true)
        );
    }

    protected function initObject()
    {
        $this->token = bin2hex(openssl_random_pseudo_bytes(20));
        $this->ctm = new \MongoDB\BSON\UTCDateTime((int)(microtime(true) * 1000));
        $this->enable = true;
        $this->lastAccess = null;
        $this->accessCount = 0;
    }

    /*
     *  Update the last use date
     */
    public function touch()
    {
        $query = array('_id' => $this->_id);

        $now = new \MongoDB\BSON\UTCDateTime((int)(microtime(true) * 1000));
        $update = array(
          '$set' => array('lastAccess' => $now),
          '$inc' => array('accessCount' => 1),
        );
        $this->__collection->updateOne($query, $update);
    }


    public function getAccessCount()
    {
        return $this->accessCount;
    }

    /**
     * Get the date of the last touch()
     */
    public function getLastAccessDate($convert2iso=false)
    {
        if ($this->lastAccess === null) {
            return null;
        }

        if ($convert2iso) {
          return $this->lastAccess->toDateTime()->format('c');
        }

        return $this->lastAccess->toDateTime();
    }

    /**
     * Get the creation time of this metadata
     */
    public function getCreationDate($convert2iso=false)
    {
        if ($convert2iso) {
            return $this->ctm->toDateTime()->format('c');
        }

        return $this->ctm->toDateTime();
    }

    /**
     * Set the owner of the token
     */
    public function setUser(MongoDBUser $user)
    {
        $this->user = $user->getId();
    }

    /**
     * Get the owner of the token
     */
    public function getUser() : MongoDBUser
    {
        $config = MongoDBBackend::getConfig();

        return new $config['user_class'](array('_id' => $this->user));
    }

    /**
     * Get the value of the token
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * Enable of the token
     */
    public function setEnable(bool $enable)
    {
        $this->enable = $enable;
    }

    /**
     * Check is the token is enable
     */
    public function isEnable() : bool
    {
        return $this->enable;
    }

    public function jsonSerialize()
    {
      return array(
        'id' => (string) $this->getId(),
        'name' => $this->getName(),
        'enable' => $this->isEnable(),
        'ctm' => $this->getCreationDate(true),
        'otm' => $this->getLastAccessDate(true),
        'count' => $this->getAccessCount(),
      );
    }
}
