<?php

namespace Everlution\TubeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TubeChainCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('everlution_tube.tube_chain')) {
            return;
        }

        $definition = $container->getDefinition('everlution_tube.tube_chain');

        foreach ($container->findTaggedServiceIds('everlution_tube.tube') as $id => $attributes) {
            $definition->addMethodCall('addTube', array($id, new Reference($id)));
        }
    }
}
