<?php

namespace Massive\Bundle\BuildBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Massive\Bundle\BuildBundle\DependencyInjection\MassiveBuildExtension;

class MassiveArtBuildExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new MassiveBuildExtension()
        );
    }

    public function testRegistration()
    {
        $this->load();
        $this->assertContainerBuilderHasService('massive_build.build.registry');
    }

    public function testLoadWithTargets()
    {
        $this->load(array(
            'targets' => array(
                'all' => array(
                    'dependencies' => array('one' => array(), 'two' => array(), 'three' => array())
                )
            ),
        ));

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
            1, array('one', 'two', 'three')
        );
    }
}
