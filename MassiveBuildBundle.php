<?php

namespace Massive\Bundle\BuildBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Massive\Bundle\BuildBundle\DependencyInjection\Compiler\BuilderPass;

class MassiveBuildBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new BuilderPass());
    }
}
