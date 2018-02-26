<?php

namespace photon\auth;

class MongoDBTemplateTag extends \photon\template\tag\Tag
{
    public $name;

    public function start($name)
    {
        $this->name = $name;
    }

    public function genStart()
    {
        return '
      $access = false;
      if (is_array($_etag->name) === false) {
          $_etag->name = array($_etag->name);
      }
      foreach($_etag->name as $name) {
          $rc = forward_static_call(array("\photon\auth\MongoDBPrecondition", $name), $t->_vars->request);
          if ($rc === true) {
              $access = true;
              break;
          }
      }
      if ($access === true) {';
    }

    public function genEnd()
    {
        return '}';
    }
}
