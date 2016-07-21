<?php

namespace Adadgio\GearBundle\NodeRed\Configuration;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Config\FileLocator;

class FlowBuilder
{
    private $config;
    private $locator;

    public function __construct(FileLocator $locator, array $config)
    {
        $this->config = $config;
        $this->locator = $locator;
    }

    public function installFlows()
    {
        // read flows from the config file
        $configurationClassNamespace = $this->config['flows']['configuration_class'];
        $configurationClass = $this->createConfigurationClass($configurationClassNamespace);

        // execute the "configure" method of the configuration class
        $flows = $configurationClass
            ->configure()
            ->getFlows();

        $flowsArray = array();
        foreach ($flows as $index => $flow) {
            // "flow" is an instance if Adadgio\GearBundle\NodeRed\Configuration\Flow
            $templatesDir = $this
                ->locator
                ->locate('@AdadgioGearBundle/Resources/nodered');

            $flow
                ->injectConfig($index, $this->config, $templatesDir) // necessary for dynamic flows urls and index
                ->parseFlow();

            // set the tabs...
            print_r($flow->getJson());
            $flowsArray = array_merge($flowsArray, $flow->getArray());
        }
        
        $this->saveFlowFile($flowsArray);
    }

    private function saveFlowFile(array $flows)
    {
        $output = $this->config['flows']['output'].'/flows.json';
        $contents = json_encode($flows);

        return file_put_contents($output, $contents);
    }

    private function createConfigurationClass($namespace)
    {
        return (new \ReflectionClass($namespace))->newInstance();
    }
}
