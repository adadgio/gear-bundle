<?php

namespace Adadgio\GearBundle\Component\Api\Authenticator;

class AuthProvider implements AuthProviderInterface
{
    protected $request;

    protected $isAuthenticated = false;

    public function configure(array $config = array())
    {
        $this->config = $config;

        return $this;
    }
    
    public function authenticate()
    {
        return true;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(\Adadgio\GearBundle\Component\Api\ApiRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    public function isAuthenticated()
    {
        return $this->authenticate();
    }
}
