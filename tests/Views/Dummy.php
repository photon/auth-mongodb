<?php

namespace tests\Views;

class Dummy
{
  public $dummy_precond = array(
  '\photon\auth\MongoDBPrecondition::adminPanel'
  );
  public function dummy($request, $match)
  {
    return new \photon\http\response\NoContent;
  }
}
