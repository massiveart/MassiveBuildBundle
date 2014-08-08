<?php

namespace MassiveArt\Bundle\BuildBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MassiveArt\Bundle\BuildBundle\DependencyInjection\MassiveArtBuildExtension;

class MassiveArtBuildExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new MassiveArtBuildExtension()
        );
    }

    public function testRegistration()
    {
        $this->load();
        $this->assertContainerBuilderHasService('massive_build.build.registry');
    }
}
