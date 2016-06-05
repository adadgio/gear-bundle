<?php

namespace Adadgio\GearBundle\Component\Api;

use Adadgio\GearBundle\Component\Api;
use Adadgio\GearBundle\Component\Reflection\ReflectionAnalysis;

/**
 * A service aware of the config that is used in the api kernel event
 * listener to check/pass authentication methods to dedicated providers
 * check method and other security.
 */
class ApiCoreService
{
    /**
     * @var array Gear bundle "api" configuration section
     */
    private $config;

    /**
     * @var boolean
     */
    private $secured;

    /**
     * @var array
     */
    private $annotation;

    /**
     * @var object \ApiRequest
     */
    private $apiRequest;

    /**
     * @var boolean
     */
    private $error;

    /**
     * @var object Service or null
     */
    private $authService;

    /**
     * Dependecy injections, see bundle services.yml for details.
     *
     * @param array The gear bundle "api" configuration section
     */
    public function __construct(array $config, \Adadgio\GearBundle\Component\Api\Authenticator\AuthProviderInterface $authService = null)
    {
        $this->secured = true; // no auth by default
        $this->config = $config;
        $this->authService = $authService;
        $this->apiRequest = new Api\ApiRequest();
    }

    /**
     * Create an ApiRequest from a full symfony
     * request object and set other internals stuff.
     *
     * @param  object \Request
     * @return object \ApiRequest
     */
    public function handleRequest(\Symfony\Component\HttpFoundation\Request $request, $annotation)
    {
        $this->annotation = $annotation;

        // decode the json body
        $content = json_decode($request->getContent(), true);
        $content = (null === $content) ? array() : $content;

        $this->apiRequest
            ->setMethod($request->getMethod())
            ->setHeaders($request->headers->all())
            ->setContent($content)
        ;

        // run the security processes
        $this->secured = $this->processRequest($this->apiRequest, $this->annotation);

        return $this;
    }

    /**
     * Get error.
     *
     * @return array Error message and code
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set error.
     *
     * @param  string   Error message
     * @param  interger Http error code
     * @return object   \ApiCoreService
     */
    public function setError($message, $code)
    {
        $this->error = array('message' => $message, 'code' => $code);

        return $this;
    }

    /**
     * Get api request object.
     *
     * @return object \ApiRequest
     */
    public function getApiRequest()
    {
        return $this->apiRequest;
    }

    /**
     * Check if all the security requirements were passed.
     *
     * @return boolean
     */
    public function isSecured()
    {
        return $this->secured;
    }

    /**
     * Processes all internals authentications by comparing
     * annotation, bundle config, and incoming resquest
     *
     * @param  object \ApiRequest
     * @param  object \Annotation\Api
     * @return boolean If secured
     * @todo Add and process requirements options (which also convert input type(s)) in body, POST or GET data
     */
    public function processRequest(Api\ApiRequest $apiRequest, Api\Annotation\Api $annotation)
    {
        // first check that annotation method(s) and request method match, if the annotation
        // did not have the method property defined, all methods are assumed to be accepted
        if ($annotation->hasProperty('method') && !in_array($apiRequest->getMethod(), $annotation->getProperty('method'))) {
            // throw new Api\ApiException(sprintf('Api route not found for method "%s"', $apiRequest->getMethod()));
            $this->setError(sprintf('Api route not found for method %s', $apiRequest->getMethod()), 404);
            return false;
        }

        // authentication can be enabled or disabled in annotations
        if ($annotation->hasProperty('enabled') && $annotation->getProperty('enabled') === false) {
            // then return always authenticated
            return true;
        }

        // process annotation requirements
        // (... not implemented yet)


        // retrive which provider should handle the authentication
        $provider = $this->getAuthenticationProvider();

        // check the class is well an authenticator, aiit!
        if (!$provider instanceof Api\Authenticator\AuthProviderInterface) {
            throw new Api\ApiException('The authentication provider must implement \Adadgio\GearBundle\Component\Api\Authenticator\AuthProviderInterface', 500);
        }

        // dispatch the authentication method to a provider service and run
        // the three require methods for autentication (same for service or simple class)
        $provider
            ->setRequest($apiRequest)
            ->configure($this->config['auth'])
            ->authenticate();

        if ($provider->isAuthenticated()) {
            return true;
        } else {
            $this->setError(sprintf('Unauthorized', $apiRequest->getMethod()), 401);
            return false;
        }
    }
    
    /**
     * @todo Check requirements
     */
    private function handleRequirements()
    {

    }

    /**
     * Get authenticator class or service
     *
     * @return object Provider instanciated class or service.
     */
    private function getAuthenticationProvider()
    {
        // use the specified authenticator (default built-in class or custom
        // class or service) to handle the authentication process
        if (null === $this->config['auth']['class'] && null === $this->config['auth']['provider']) {
            // when neither "class" or "provider" are defined, rely on the
            // auth type to determine which class must handle the authentication
            $provider = $this->createAuthenticationProviderInstance($this->config['auth']['type']);

        } else if (null !== $this->config['auth']['class']) {
            // if the provider simple "class" is defined we use it. A simple class
            // must extend the "AuthProvider" base class and implement AuthProviderInterface.
            $class = $this->config['auth']['class'];
            $provider = new $class();

        } else if (null !== $this->config['auth']['provider']) {
            // else the provider can be a service (see "service" option). In this case it
            // must have the same methods as a simple class (except constructor you can use to inject
            // other service) and extend the "AuthProvider" base class and implement AuthProviderInterface. as well
            $provider = $this->authService;

        } else {
            // else use a default authenticator (the base "AuthProvider" which just returns true)
            $provider = new Authenticator\AuthProvider();
        }

        return $provider;
    }

    /**
     * Create an instance of an auth provider class from a
     * classifiedauth provider class name from auth type keyword.
     *
     * @param string Auth type keyword (Basic, ApiKey, HeaderKey, ...)
     * @return object \AuthProviderInterface
     */
    private function createAuthenticationProviderInstance($authType = null)
    {
        $namespace = 'Adadgio\GearBundle\Component\Api\Authenticator';
        $class = $namespace.'\\'.$authType.'AuthProvider';

        return new $class();
    }
}
