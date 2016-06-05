<?php

namespace Adadgio\GearBundle\Component\Api\Authenticator;

interface AuthProviderInterface
{
    public function configure(array $config);

    public function authenticate();

    public function getConfig();

    public function setRequest(\Adadgio\GearBundle\Component\Api\ApiRequest $request);

    public function isAuthenticated();
}
