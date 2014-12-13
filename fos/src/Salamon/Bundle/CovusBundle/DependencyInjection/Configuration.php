<?php

namespace Salamon\Bundle\CovusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use FOS\UserBundle\DependencyInjection\Configuration as BaseConfig;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    private function addCovusSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('covus')
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
            ->arrayNode('form')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('type')->defaultValue('fos_user_alltrue')->end()
            ->scalarNode('name')->defaultValue('fos_user_alltrue_form')->end()
            ->arrayNode('validation_groups')
            ->prototype('scalar')->end()
            ->defaultValue(array('covus', 'Default'))
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_user');
        $this->addCovusSection($rootNode);
        return $treeBuilder;
    }

}
