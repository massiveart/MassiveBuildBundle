<?php

namespace MassiveArt\Bundle\BuildBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Prophecy\PhpUnit\ProphecyTestCase;
use MassiveArt\Bundle\BuildBundle\Command\BuildCommand;
use MassiveArt\Bundle\BuildBundle\Build\BuilderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Prophecy\Argument;

class BuildCommandTest extends ProphecyTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->buildRegistry = $this->prophesize('MassiveArt\Bundle\BuildBundle\Build\BuildRegistry');
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->builder1 = $this->prophesize('MassiveArt\Bundle\BuildBundle\Build\BuilderInterface');
        $this->builder1->getName()->willReturn('Builder 1');
        $this->builder2 = $this->prophesize('MassiveArt\Bundle\BuildBundle\Tests\Command\TestContainerAwareBuilder');
        $this->builder2->getName()->willReturn('Builder 2');

        $this->command = new BuildCommand(
            $this->buildRegistry->reveal(),
            $this->container->reveal()
        );

        $this->tester = new CommandTester($this->command);
    }

    public function testBuildNoTargetNoBuilders()
    {
        $this->buildRegistry->getBuilders(null)->willReturn(array());
        $res = $this->tester->execute(array(), array());
        $this->assertEquals(0, $res);
    }

    public function testBuildNoTarget()
    {
        $this->buildRegistry->getBuilders(null)->willReturn(array(
            $this->builder1->reveal(),
            $this->builder2->reveal()
        ));

        $this->builder2->setContainer($this->container)->shouldBeCalled();

        $this->builder1->build(
            Argument::type('Symfony\Component\Console\Input\InputInterface'),
            Argument::type('Symfony\Component\Console\Output\OutputInterface')
        )->shouldBeCalled();
        $this->builder2->build(
            Argument::type('Symfony\Component\Console\Input\InputInterface'),
            Argument::type('Symfony\Component\Console\Output\OutputInterface')
        )->shouldBeCalled();


        $res = $this->tester->execute(array(), array());
        $this->assertEquals(0, $res);
    }
}

interface TestContainerAwareBuilder extends BuilderInterface, ContainerAwareInterface
{
}
