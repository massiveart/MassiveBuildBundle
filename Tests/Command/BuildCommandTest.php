<?php

namespace Massive\Bundle\BuildBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Prophecy\PhpUnit\ProphecyTestCase;
use Massive\Bundle\BuildBundle\Command\BuildCommand;
use Massive\Bundle\BuildBundle\Build\BuilderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Prophecy\Argument;

class BuildCommandTest extends ProphecyTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->buildRegistry = $this->prophesize('Massive\Bundle\BuildBundle\Build\BuildRegistry');
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->builder1 = $this->prophesize('Massive\Bundle\BuildBundle\Build\BuilderInterface');
        $this->builder2 = $this->prophesize('Massive\Bundle\BuildBundle\Tests\Command\TestContainerAwareBuilder');

        $this->setupBuilder($this->builder1, 'Builder 1', array('Builder 2'));
        $this->setupBuilder($this->builder2, 'Builder 2', array());

        $this->command = new BuildCommand(
            $this->buildRegistry->reveal(),
            $this->container->reveal()
        );
        $this->command->addOption('verbose');
        $this->command->addOption('no-interaction');

        $this->tester = new CommandTester($this->command);
    }

    protected function execute(array $input, $options)
    {
        return $this->tester->execute(array_merge(array(
            '--verbose' => true,
            '--no-interaction' => true
        ), $input), $options);
    }

    protected function setupBuilder($builder, $title, $dependencies)
    {
        $builder->getName()->willReturn($title);
        $builder->getDependencies()->willReturn($dependencies);
    }

    public function testBuildNoTargetNoBuilders()
    {
        $this->buildRegistry->getBuilders(null)->willReturn(array());
        $res = $this->execute(array(), array());
        $this->assertEquals(0, $res);
    }

    public function testBuildTarget()
    {
        $this->buildRegistry->getBuilders('Builder 1')->willReturn(array(
            $this->builder1->reveal(),
            $this->builder2->reveal()
        ));

        $this->builder2->setContainer($this->container)->shouldBeCalled();
        $this->builder1->setContext(Argument::type('Massive\Bundle\BuildBundle\Build\BuilderContext'))
            ->shouldBeCalled();
        $this->builder2->setContext(Argument::type('Massive\Bundle\BuildBundle\Build\BuilderContext'))
            ->shouldBeCalled();

        $this->builder1->build()->shouldBeCalled();
        $this->builder2->build()->shouldBeCalled();

        $res = $this->execute(array('target' => 'Builder 1'), array());
        $this->assertEquals(0, $res);
    }

    public function testBuildNotTarget()
    {
        $this->buildRegistry->getBuilders(null)->willReturn(array(
            $this->builder1->reveal(),
            $this->builder2->reveal()
        ));

        $this->execute(array(), array());
        $display = $this->tester->getDisplay();
        $this->assertContains('Builder 1', $display);
        $this->assertContains('Builder 2', $display);
    }
}

interface TestContainerAwareBuilder extends BuilderInterface, ContainerAwareInterface
{
}
