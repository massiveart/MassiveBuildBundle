<?php

namespace Massive\Bundle\BuildBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class BuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(
            'massive_build.build.registry'
        )) {
            return;
        }

        $buildRegistry = $container->getDefinition('massive_build.build.registry');
        $ids = $container->findTaggedServiceIds('massive_build.builder');

        foreach (array_keys($ids) as $id) {
            $buildRegistry->addMethodCall(
                'addBuilder',
                array(new Reference($id))
            );
        }
    }
}
