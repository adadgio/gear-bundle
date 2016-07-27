<?php

namespace Adadgio\GearBundle\NodeRed\Configuration;

use Adadgio\GearBundle\NodeRed\Flow\ConfigurationInterface;

class FlowConfiguration implements FlowConfigurationInterface
{
    protected $flows;
    protected $settings;
    
    public function __construct()
    {
        $this->flows = array();
        $this->settings = new Settings();
    }

    public function configure()
    {
        return $this;
    }

    /**
     * Get flows class objects.
     *
     * @return array Of \Flow(s)
     */
    public function getFlows()
    {
        return $this->flows;
    }

    /**
     * Get settings class object.
     *
     * @return object \Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Apply a user function to each flows.
     *
     * @param  callable A callback function
     * @return \FlowConfigurationInterface
     */
    public function forEachFlow(callable $function) // $method, $args = null
    {
        foreach ($this->flows as $i => $flow) {
            $this->flows[$i] = call_user_func($function, $flow);
        }

        return $this;
    }
}
