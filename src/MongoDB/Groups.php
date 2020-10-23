<?php

namespace photon\auth\MongoDB;

use photon\auth\MongoDBGroup;

trait Groups
{
  /**
   *  Test if the $group is in the group
   */
    public function containsGroup(MongoDBGroup $group)
    {
        if (in_array($group->_id, (array)$this->groups)) {
            return true;
        }

        return false;
    }

  /**
   *  Add the $group in the group
   *  You must call save() after to sync into database
   */
    public function addGroup(MongoDBGroup $group)
    {
        $id = $group->_id;
        $groups = (array)$this->groups;

        if (in_array($id, $groups) === false) {
            $groups[] = $id;
        }

        $this->groups = $groups;
    }

  /**
   *  Remove the $group from the group
   *  You must call save() after to sync into database
   */
    public function removeGroup(MongoDBGroup $group)
    {
        $id = $group->_id;
        $groups = $this->groups;

        $keys = array_keys($groups, $id);
        foreach ($keys as $k) {
            unset($groups[$k]);
        }

        $this->groups = array_values($groups);
    }

    /**
     *  Get the user list
     */
    public function getGroups($string=false)
    {
      $groups = (array) $this->groups;

      if ($string) {
        $groups = array_map(function($i) {
          return (string) $i;
        }, $groups);
      }

      return $groups;
    }
}
