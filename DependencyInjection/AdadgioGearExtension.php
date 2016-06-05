<?php

namespace Adadgio\GearBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AdadgioGearExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // set all bundle parameters from configuration values
        $this->setBundleParameters($container, $config);
    }
    
    /**
     * Set container parameters
     *
     * @param object Container
     * @param array  Bundle config
     */
    private function setBundleParameters($container, array $config)
    {
        // nodered configuration
        $container->setParameter('adadgio_gear.nodered', $config['nodered']);

        // set all NodeRed parameters
        foreach ($config['nodered'] as $key => $value) {
            $name = sprintf('adadgio_gear.nodered.%s', $key);
            $container->setParameter($name, $value);
        }

        // api configuration
        $container->setParameter('adadgio_gear.api', $config['api']);
        // also add dynamic api.auth.service option (can be null, by default, see also bundle services.yml declaration)
        $container->setParameter('adadgio_gear.api.auth.provider', $config['api']['auth']['provider']);
    }
}
