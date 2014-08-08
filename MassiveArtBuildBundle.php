<?php

namespace MassiveArt\Bundle\BuildBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use MassiveArt\Bundle\BuildBundle\DependencyInjection\Compiler\BuilderPass;

class MassiveArtBuildBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new BuilderPass());
    }
}
