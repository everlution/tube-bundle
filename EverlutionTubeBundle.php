<?php

namespace Everlution\TubeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Everlution\TubeBundle\DependencyInjection\Compiler\TubeChainCompilerPass;

class EverlutionTubeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TubeChainCompilerPass());
    }
}
