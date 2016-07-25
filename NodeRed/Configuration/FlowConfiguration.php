<?php

namespace Adadgio\GearBundle\NodeRed\Configuration;

use Adadgio\GearBundle\NodeRed\Flow\ConfigurationInterface;

class FlowConfiguration implements FlowConfigurationInterface
{
    protected $flows;
    
    public function __construct()
    {
        $this->flows = array();
    }

    public function configure()
    {
        return $this;
    }

    public function getFlows()
    {
        return $this->flows;
    }
}
