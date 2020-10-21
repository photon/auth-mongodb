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
        $config = MongoDBBackend::getConfig();
        $class = $config['precondition_class'];
        
        return '
      $access = false;
      if (is_array($_etag->name) === false) {
          $_etag->name = array($_etag->name);
      }
      foreach($_etag->name as $name) {
          $rc = forward_static_call(array("' . $class . '", $name), $t->_vars->request);
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
