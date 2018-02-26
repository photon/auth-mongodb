<?php

namespace photon\auth\MongoDB;

trait Name
{
  /**
   *  Set the $name
   */
    public function setName($name)
    {
        $this->name = $name;
    }

  /**
   *  Get the name
   */
    public function getName()
    {
        return $this->name;
    }
}
