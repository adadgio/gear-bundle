<?php

namespace Adadgio\GearBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('adadgio_gear');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode('nodered')
                    ->children()
                        ->scalarNode('host')->isRequired()->end()
                        ->scalarNode('port')->defaultValue(1880)->end()
                        ->scalarNode('protocol')->defaultValue('http://')->end()
                        ->arrayNode('http_auth')
                            ->children()
                                ->scalarNode('user')->defaultValue(null)->end()
                                ->scalarNode('pass')->defaultValue(null)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
