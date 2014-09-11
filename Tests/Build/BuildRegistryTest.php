<?php

namespace Massive\Bundle\BuildBundle\Tests\Build;

use Prophecy\PhpUnit\ProphecyTestCase;
use Massive\Bundle\BuildBundle\Build\BuildRegistry;

class BuildRegistryTest extends ProphecyTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->buildRegistry = new BuildRegistry();
    }

    protected function createBuilder($name, $dependencies = array())
    {
        $builder = $this->prophesize('Massive\Bundle\BuildBundle\Build\BuilderInterface');
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
        $builder1->getDependencies()->willReturn(array('foobar'));
        $this->buildRegistry->addBuilder($builder1->reveal());
        $this->buildRegistry->getBuilders();
    }

    public function testBuilderDependencies()
    {
        $b1 = $this->createBuilder('builder1', array('builder3', 'builder4'));
        $b2 = $this->createBuilder('builder2', array());
        $b3 = $this->createBuilder('builder3', array('builder4'));
        $b4 = $this->createBuilder('builder4', array());

        $this->buildRegistry->addBuilder($b1->reveal());
        $this->buildRegistry->addBuilder($b2->reveal());
        $this->buildRegistry->addBuilder($b3->reveal());
        $this->buildRegistry->addBuilder($b4->reveal());

        $builders = $this->buildRegistry->getBuilders();

        $builderNames = array();
        foreach ($builders as $builder) {
            $builderNames[] = $builder->getName();
        }

        $this->assertEquals(array('builder4', 'builder2', 'builder3', 'builder1'), $builderNames);
    }

    public function testGetBuildersForTarget()
    {
        $this->buildRegistry->addBuilder($this->createBuilder('builder1')->reveal());
        $this->buildRegistry->addBuilder($this->createBuilder('builder2')->reveal());
        $this->buildRegistry->addBuilder($this->createBuilder('builder3', array('builder1'))->reveal());
        $this->buildRegistry->addBuilder($this->createBuilder('builder4', array('builder3'))->reveal());

        $builders = $this->buildRegistry->getBuilders('builder4');

        $builderNames = array();
        foreach ($builders as $builder) {
            $builderNames[] = $builder->getName();
        }

        $this->assertEquals(array('builder1', 'builder3', 'builder4'), $builderNames);
    }
}
