<?php

namespace photon\auth;

/*
 *  Generic precondition handler for photon views
 *  The name of the function called is converted in ACL name
 */
class MongoDBPrecondition
{
    public static function __callStatic($name, $arguments)
    {
        $request = $arguments[0];

        // Ensure an user is connected
        if (isset($request->user) === false) {
            return new \photon\http\response\Forbidden($request);
        }

        // Load the ACL and verify
        try {
            $config = MongoDBBackend::getConfig();
            $class = $config['acl_class'];
            $acl = new $class(array('name' => $name));
            if ($acl->isAllow($request->user)) {
                return true;
            }
        } catch (\Exception $e) {
            return new \photon\http\response\Forbidden($request);
        }

        return new \photon\http\response\Forbidden($request);
    }
}
