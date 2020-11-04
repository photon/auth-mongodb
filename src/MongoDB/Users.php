<?php

namespace photon\auth\MongoDB;

use photon\auth\MongoDBUser;

trait Users
{
  /**
   *  Test if the $user is in the group
   */
    public function containsUser(MongoDBUser $user)
    {
        if (in_array($user->_id, (array)$this->users)) {
            return true;
        }

        return false;
    }

  /**
   *  Add the $user in the group
   *  You must call save() after to sync into database
   */
    public function addUser(MongoDBUser $user)
    {
        $id = $user->_id;
        $users = (array)$this->users;

        if (in_array($id, $users) === false) {
            $users[] = $id;
        }

        $this->users = $users;
    }

  /**
   *  Remove the $user from the group
   *  You must call save() after to sync into database
   */
    public function removeUser(MongoDBUser $user)
    {
        $id = $user->_id;
        $users = $this->users;

        $keys = array_keys($users, $id);
        foreach ($keys as $k) {
            unset($users[$k]);
        }

        $this->users = array_values($users);
    }

    /**
     *  Get the user list
     */
    public function getUsers($string = false)
    {
        $users = (array) $this->users;

        if ($string) {
            $users = array_map(function ($i) {
                return (string) $i;
            }, $users);
        }

        return $users;
    }
}
