<?php

namespace Adadgio\GearBundle\Component\Api\Authenticator;

class ExampleAuthProviderService extends AuthProvider implements AuthProviderInterface
{
    /**
     * Build your service like you build services every day
     */
    public function __construct()
    {
        
    }

    /**
     * Checks a authentication Basic method. You can access
     * config through getConfig of parent class.
     *
     * @return boolean
     */
    public function authenticate()
    {
        // your owns logic here
        $request = $this->getRequest();
        $headers = $request->getHeaders();

        return true;
    }
}
