<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Tests\Build;

use Massive\Bundle\BuildBundle\Build\BuilderInterface;
use Massive\Bundle\BuildBundle\Build\BuildRegistry;

class BuildRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BuildRegistry
     */
    private $buildRegistry;

    public function setUp()
    {
        parent::setUp();

        $this->buildRegistry = new BuildRegistry();
    }

    protected function createBuilder($name, $dependencies = [])
    {
        $builder = $this->prophesize(BuilderInterface::class);
        $builder->getName()->willReturn($name);
        $builder->getDependencies()->willReturn($dependencies);

        return $builder;
    }

    public function testBuildRegistryAddAndGet()
    {
        $this->createBuilder('builder1');
        $builders = $this->buildRegistry->getBuilders();
        $this->assertNotNull($builders);
        $this->assertCount(0, $builders);

        $builder1 = $this->createBuilder('builder1');
        $builder2 = $this->createBuilder('builder2');

        $this->buildRegistry->addBuilder($builder1->reveal());
        $this->buildRegistry->addBuilder($builder2->reveal());

        $this->assertCount(2, $this->buildRegistry->getBuilders());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage has already been added
     */
    public function testAddExisting()
    {
        $builder1 = $this->createBuilder('builder1');
        $this->buildRegistry->addBuilder($builder1->reveal());
        $this->buildRegistry->addBuilder($builder1->reveal());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage non-existent
     */
    public function testGetBuilderNotExistent()
    {
        $this->buildRegistry->getBuilder('non-existent');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage unknown builder
     */
    public function testBuilderDependenciesMissingDep()
    {
        $builder1 = $this->createBuilder('builder1');
        $builder1->getDependencies()->willReturn(['foobar']);
        $this->buildRegistry->addBuilder($builder1->reveal());
        $this->buildRegistry->getBuilders();
    }

    public function testBuilderDependencies()
    {
        $b1 = $this->createBuilder('builder1', ['builder3', 'builder4']);
        $b2 = $this->createBuilder('builder2', []);
        $b3 = $this->createBuilder('builder3', ['builder4']);
        $b4 = $this->createBuilder('builder4', []);

        $this->buildRegistry->addBuilder($b1->reveal());
        $this->buildRegistry->addBuilder($b2->reveal());
        $this->buildRegistry->addBuilder($b3->reveal());
        $this->buildRegistry->addBuilder($b4->reveal());

        $builders = $this->buildRegistry->getBuilders();

        $builderNames = [];
        foreach ($builders as $builder) {
            $builderNames[] = $builder->getName();
        }

        $this->assertTrue(\array_search('builder4', $builderNames) < \array_search('builder3', $builderNames));
        $this->assertTrue(\array_search('builder3', $builderNames) < \array_search('builder1', $builderNames));
        $this->assertTrue(\array_search('builder4', $builderNames) < \array_search('builder1', $builderNames));
    }

    public function testGetBuildersForTarget()
    {
        $this->buildRegistry->addBuilder($this->createBuilder('builder1')->reveal());
        $this->buildRegistry->addBuilder($this->createBuilder('builder2')->reveal());
        $this->buildRegistry->addBuilder($this->createBuilder('builder3', ['builder1'])->reveal());
        $this->buildRegistry->addBuilder($this->createBuilder('builder4', ['builder3'])->reveal());

        $builders = $this->buildRegistry->getBuilders('builder4');

        $builderNames = [];
        foreach ($builders as $builder) {
            $builderNames[] = $builder->getName();
        }

        $this->assertEquals(['builder1', 'builder3', 'builder4'], $builderNames);
    }
}
