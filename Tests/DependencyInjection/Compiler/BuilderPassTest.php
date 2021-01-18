<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Tests\DependencyInjection\Compiler;

use Massive\Bundle\BuildBundle\DependencyInjection\Compiler\BuilderPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BuilderPassTest extends AbstractCompilerPassTestCase
{
    public function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BuilderPass());
    }

    public function testBuilderRegistration()
    {
        $builderRegistry = new Definition();
        $this->setDefinition('massive_build.build.registry', $builderRegistry);

        $builder1 = new Definition();
        $builder1->addTag('massive_build.builder');
        $this->setDefinition('build_1', $builder1);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'massive_build.build.registry',
            'addBuilder',
            [
                new Reference('build_1'),
            ]
        );
    }
}
