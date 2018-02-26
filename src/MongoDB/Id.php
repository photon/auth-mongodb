<?php

namespace photon\auth\MongoDB;

trait Id
{

  /**
   *  Get the object id
   */
    public function getId()
    {
        return $this->_id;
    }
}
