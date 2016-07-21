<?php

namespace Adadgio\GearBundle\NodeRed;

/**
 * A NodeRed standardized payload to be send to HTTP endpoints.
 * A payload in NodeRed consists of a a JSON encoded message.
 */
class Payload implements PayloadInterface
{
    /**
     * @var array The payload message contents
     */
    protected $parameters;

    /**
     * Payload constructor.
     *
     * @param array Payload contents
     */
    public function __construct(array $parameters = array())
    {
        // set default parameters for those who might be missing
        $this->parameters = $this->configureDefaults($parameters);
    }

    /**
     * Create a payload new unique process id.
     * Essentialy a utility when used in loops.
     *
     * @return string Unique pid
     */
    public static function newPid()
    {
        return uniqid();
    }

    /**
     * Set kill parameter.
     *
     * @return object \PayloadInterface
     */
	public function kill()
    {
        $this->parameters['kill'] = true;

        return $this;
    }

    /**
     * Set kill parameter.
     *
     * @return object \PayloadInterface
     */
	public function live()
    {
        $this->parameters['kill'] = false;

        return $this;
    }

    /**
     * Set because parameter (essentialy a utility)
     *
     * @param  string
     * @return object \PayloadInterface
     */
    public function because($because)
    {
        $this->parameters['because'] = $because;

        return $this;
    }

    /**
     * Get current iteration parameter.
     *
     * @return integer
     */
    public function getIteration()
    {
        return $this->parameters['iteration'];
    }

    /**
     * Get payload response contents (parameters).
     *
     * @return array Payload response message
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get any custom parameter value.
     *
     * @param  string Param name
     * @return mixed  Param value
     */
    public function getParameter($key)
    {
        return isset($this->message[$key]) ? $this->message[$key] : null;
    }

    /**
     * Set any custom parameter value,reserved params excluded.
     *
     * @param string Parameter name
     * @param object \PayloadInterface
     */
    public function setParameter($key, $value)
    {
        if (in_array($key, $this->reserved)) {
            throw new \GearException('You cannot set the "%s" parameter manually on a NodeRed Payload object because it is a reserved parameter', $key);
        }

        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * Unset/Removes a payload specific parameter.
     *
     * @param string Param name
     * @param object \PayloadInterface
     */
    public function removeParameter($param)
    {
        if ($this->hasParameter($param)) {
            unset($this->message[$param]);
        }

        return $this;
    }

    /**
     * Add many parameters to the payload at once.
     *
     * @param array Key/value pairs params
     * @param object \PayloadInterface
     */
    public function addParameters(array $params = array())
    {
        foreach ($params as $key => $value) {
            if (!$this->hasParameter($key)) {
                $this->setParameter($key, $value);
            }
        }

        return $this;
    }

    /**
     * Check it the payload has a parameter key.
     *
     * @param string Param key
     * @return boolean
     */
    public function hasParameter($key)
    {
        return isset($this->message[$key]) ? true : false;
    }

    /**
     * Set default parameters when they are not given in constructor.
     *
     * @param array Input constructor parameters
     * @param array Merged default/input parameters
     */
    public function configureDefaults(array $parameters = array())
    {
        $parameters['pid'] = !isset($parameters['pid']) ? self::newPid() : $parameters['pid'];
        $parameters['kill'] = !isset($parameters['kill']) ? false : (bool) $parameters['kill'];
        $parameters['iteration'] = !isset($parameters['iteration']) ? -1 : ($parameters['iteration'] + 1);

        return $parameters;
    }
}
