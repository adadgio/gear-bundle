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

                ->arrayNode('nodered')->addDefaultsIfNotSet()
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

                        // "flows" configuration node
                        ->append($this->setNodeRedFlowsConfiguration())
                        // "settings" configuration node
                        ->append($this->setNodeRedSettingsConfiguration())

                    ->end()
                ->end()

                ->arrayNode('api')->addDefaultsIfNotSet()
                    ->children()

                        ->arrayNode('auth')->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('type')->defaultValue(null)
                                    ->values(array('Basic', 'Client', 'Headers', 'Static', null))
                                ->end()
                                ->scalarNode('class')->defaultValue(null)->end()
                                ->scalarNode('provider')->defaultValue(null)->end()
                                ->scalarNode('user')->defaultValue(null)->end()
                                ->scalarNode('password')->defaultValue(null)->end()

                                ->arrayNode('clients')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('id')->defaultValue(null)->end()
                                            ->scalarNode('secret')->defaultValue(null)->end()
                                            ->booleanNode('enabled')->defaultValue(true)->end()
                                        ->end()
                                    ->end()
                                ->end()

                            ->end()
                        ->end()

                    ->end()
                ->end()

                ->arrayNode('serialization')->isRequired()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->arrayNode('fields')->isRequired()->cannotBeEmpty()
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->cannotBeEmpty()->defaultValue(null)->end()
                                        ->scalarNode('method')->cannotBeEmpty()->defaultValue(null)->end()
                                        ->scalarNode('arg')->cannotBeEmpty()->defaultValue(null)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end();

        return $treeBuilder;
    }

    private function setNodeRedSettingsConfiguration()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('settings')->isRequired();

        return $node
            ->children()

                ->integerNode('ui_port')->defaultValue(1880)->end()
                ->arrayNode('admin_auth')
                    ->children()
                        ->scalarNode('type')->defaultValue('credentials')->end()
                        ->arrayNode('users')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('username')->defaultValue('demo')->end()
                                    ->scalarNode('password')->defaultValue('dEm0')->end()
                                    ->scalarNode('permissions')->defaultValue('*')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('flow_file')->defaultValue(null)->end()
                ->scalarNode('user_dir')->defaultValue(null)->end()
                ->scalarNode('nodes_dir')->defaultValue(null)->end()
                ->arrayNode('http_node_auth')->treatNullLike(array())
                    ->children()
                        ->scalarNode('user')->defaultValue(null)->end()
                        ->scalarNode('pass')->defaultValue(null)->end()
                    ->end()
                ->end()
                ->scalarNode('http_admin_root')->defaultValue(false)->end()

            ->end();
    }

    private function setNodeRedFlowsConfiguration()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('flows');

        return $node
            ->children()
                ->scalarNode('output')->defaultValue('%kernel.cache_dir%')->end()
                ->scalarNode('configuration_class')->defaultValue('Adadgio\GearBundle\NodeRed\Configuration\Configuration')->end()
                ->arrayNode('parameters')
                    ->prototype('scalar')->defaultValue(null)->end()
                ->end()
            ->end();
    }
}
