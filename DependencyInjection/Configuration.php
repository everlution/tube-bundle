<?php

namespace Everlution\TubeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('everlution_tube');

        $rootNode
            ->children()
                ->scalarNode('job_class')
                    ->cannotBeEmpty()
                    ->defaultValue('Everlution\TubeBundle\Model\Job')
                ->end()
                ->scalarNode('job_serializer')
                    ->cannotBeEmpty()
                    ->defaultValue('Everlution\TubeBundle\Serializer\DefaultJsonJobSerializer')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
