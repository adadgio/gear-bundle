<?php

namespace Adadgio\GearBundle\Component\Api\Authenticator;

class BasicAuthProvider extends AuthProvider implements AuthProviderInterface
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

        // no Authorization at all
        if (!isset($headers['authorization'][0])) {
            return false;
        }

        // note Due to an Apache2 bug (or Sf?) the "Authorization" header never exists in a request. See also
        // http://stackoverflow.com/questions/19443718/symfony-2-3-getrequest-headers-not-showing-authorization-bearer-token

        $config = $this->getConfig();

        $user = $config['user'];
        $password = $config['password'];
        $base64 = base64_encode($user.':'.$password);

        // user input authorization pair base64 user:password
        $auth = $this->getAuthString($headers);

        // check auth
        if ($auth === $base64) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Custom method to find user input auth string from
     * the header "Basic: <base64String>"
     *
     * @return string
     */
    private function getAuthString($headers)
    {
        $header = $headers['authorization'][0];
        $parts = explode('Basic:', $header);

        $parts = array_map('trim', $parts);
        return end($parts);
    }
}
