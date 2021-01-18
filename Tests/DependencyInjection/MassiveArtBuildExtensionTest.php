<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Tests\DependencyInjection;

use Massive\Bundle\BuildBundle\DependencyInjection\MassiveBuildExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class MassiveArtBuildExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new MassiveBuildExtension(),
        ];
    }

    public function testRegistration()
    {
        $this->load();
        $this->assertContainerBuilderHasService('massive_build.build.registry');
    }

    public function testLoadWithTargets()
    {
        $this->load([
            'targets' => [
                'all' => [
                    'dependencies' => ['one' => [], 'two' => [], 'three' => []],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'massive_build.builder.virtual.all',
            'massive_build.builder'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'massive_build.builder.virtual.all',
            0, 'all'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'massive_build.builder.virtual.all',
            1, ['one', 'two', 'three']
        );
    }
}
