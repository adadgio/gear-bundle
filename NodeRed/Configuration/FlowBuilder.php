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

    /**
     * Change default parameter for output dir.
     *
     * @param string New output directory for flows and settings.
     * @return object \FlowBuilder
     */
    public function setOutput($dir)
    {
        $this->config['flows']['output'] = $dir;

        return $this;
    }

    /**
     *
     * @return object \FlowBuilder
     */
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
            $flowsArray = array_merge($flowsArray, $flow->getArray());
        }

        $bool = $this->saveFlowFile($flowsArray);
        return $this;
    }



    public function installSettings()
    {
        $configurationClassNamespace = $this->config['flows']['configuration_class'];
        $configurationClass = $this->createConfigurationClass($configurationClassNamespace);

        $settingDir = $this
            ->locator
            ->locate('@AdadgioGearBundle/Resources/nodered');

        $settings = $configurationClass
            ->configure()
            ->getSettings();

        $contents = $settings
            ->injectConfig($this->config, $settingDir) // necessary for dynamic flows urls and index
            ->parseSettings()
            ->getContents();

        $bool = $this->saveSettingsFile($contents);
        return $this;
    }

    /**
     * Create a configuration class that extends FlowConfiguration by reflection.
     *
     * @param  string Class namespace
     * @return object \FlowConfigurationInterface
     */
    private function createConfigurationClass($namespace)
    {
        return (new \ReflectionClass($namespace))->newInstance();
    }

    /**
     * Save a flow file in the output directory.
     *
     * @param  array
     * @return boolean
     */
    private function saveFlowFile(array $flows)
    {
        $output = $this->config['flows']['output'].'/flows.json';
        $contents = json_encode($flows);

        return file_put_contents($output, $contents);
    }

    /**
     * Save a settings file in the output directory.
     *
     * @param  array
     * @return boolean
     */
    private function saveSettingsFile($contents)
    {
        $output = $this->config['flows']['output'].'/settings.js';

        return file_put_contents($output, $contents);
    }
}
