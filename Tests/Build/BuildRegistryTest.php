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

    protected function createBuilder($name)
    {
        $builder = $this->prophesize('Massive\Bundle\BuildBundle\Build\BuilderInterface');
        $builder->getName()->willReturn($name);

        return $builder;
    }

    protected function createTarget($name, $buildConfigs = array(), $dependencies = array())
    {
        $target = $this->prophesize('Massive\Bundle\BuildBundle\Build\Target');
        $target->getName()->willReturn($name);
        $target->getBuilderConfigs()->willReturn($buildConfigs);
        $target->getDependencies()->willReturn($dependencies);

        return $target;
    }


    public function testBuildRegistryAddAndGet()
    {
        $builder1 = $this->createBuilder('builder1');
        $builder2 = $this->createBuilder('builder2');

        $this->buildRegistry->addBuilder($builder1->reveal());
        $this->buildRegistry->addBuilder($builder2->reveal());

        $this->assertSame($builder1->reveal(), $this->buildRegistry->getBuilder('builder1'));

        $target1 = $this->createTarget('target1');
        $target2 = $this->createTarget('target2');

        $this->buildRegistry->addTarget($target1->reveal());
        $this->buildRegistry->addTarget($target2->reveal());

        $this->assertSame($target1->reveal(), $this->buildRegistry->getTarget('target1'));
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
     * @expectedExceptionMessage non-existent target
     */
    public function testTargetDependenciesMissingDep()
    {
        $target1 = $this->createTarget('target1');
        $target1->getDependencies()->willReturn(array('foo'));
        $this->buildRegistry->addTarget($target1->reveal());
        $this->buildRegistry->getTargets('target1');
    }

    public function testGetTargets()
    {
        $this->buildRegistry->addTarget($this->createTarget('target1')->reveal());
        $this->buildRegistry->addTarget($this->createTarget('target2')->reveal());
        $this->buildRegistry->addTarget($this->createTarget('target3', array(), array('target1'))->reveal());
        $this->buildRegistry->addTarget($this->createTarget('target4', array(), array('target3'))->reveal());

        $targets = $this->buildRegistry->getTargets('target4');

        $targetNames = array();
        foreach ($targets as $target) {
            $targetNames[] = $target->getName();
        }

        $this->assertEquals(array('target1', 'target3', 'target4'), $targetNames);
    }
}
