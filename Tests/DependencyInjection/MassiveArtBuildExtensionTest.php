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

    public function testTargetRegistration()
    {
        $this->load(array(
            'targets' => array(
                'all' => array(
                    'dependencies' => array('one', 'two', 'three')
                )
            ),
        ));
    }
}
