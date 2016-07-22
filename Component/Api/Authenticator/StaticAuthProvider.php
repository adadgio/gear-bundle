<?php

namespace Adadgio\GearBundle\Component\Api\Authenticator;

class StaticAuthProvider extends AuthProvider implements AuthProviderInterface
{
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

        $config = $this->getConfig();
        // $annotation = $this->getAnnotation();

        $clientIdField = $this->getAnnotation()->getProperty('with')['client_id'];
        $clientKeyField = $this->getAnnotation()->getProperty('with')['token'];

        // headers must exist, no authorization at all otherwise
        if (!isset($headers[$clientIdField][0]) OR !isset($headers[$clientKeyField][0])) {
            return false;
        }

        $headerClientId = $headers[$clientIdField][0];
        $headerClientKey = $headers[$clientKeyField][0];

        // check both client id and secret against the config providers
        foreach ($config['clients'] as $name => $staticClient) {
            if ($staticClient['id'] === $headerClientId && $staticClient['secret'] === $headerClientKey) {
                // client found and key matches!
                return true;
            }
        }

        return false;
    }
}
