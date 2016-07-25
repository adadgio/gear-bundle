<?php

namespace Adadgio\GearBundle\Component\Api\Authenticator;

class AuthProvider implements AuthProviderInterface
{
    private $config;
    private $annotation;
    private $request;
    private $isAuthenticated = false;

    public function configure(array $config = array(), \Adadgio\GearBundle\Component\Api\Annotation\Api $annotation)
    {
        $this->config = $config;
        $this->annotation = $annotation;

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

    public function getAnnotation()
    {
        return $this->annotation;
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
