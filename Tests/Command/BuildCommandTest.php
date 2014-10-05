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

        $this->target1 = $this->prophesize('Massive\Bundle\BuildBundle\Build\Target');
        $this->target2 = $this->prophesize('Massive\Bundle\BuildBundle\Build\Target');

        $this->builder1 = $this->prophesize('Massive\Bundle\BuildBundle\Tests\Command\TestContainerAwareTarget');

        $this->setupTarget($this->target1, 'Target 1', array('Target 2'));
        $this->setupTarget($this->target2, 'Target 2', array());

        $this->command = new BuildCommand(
            $this->buildRegistry->reveal(),
            $this->container->reveal()
        );
        $this->command->addOption('verbose');
        $this->command->addOption('no-interaction');

        $this->tester = new CommandTester($this->command);
    }

    public function testBuildNoTargetNoTargets()
    {
        $this->buildRegistry->getAllTargets()->willReturn(array());
        $res = $this->execute(array(), array());
        $this->assertEquals(0, $res);
    }

    public function testBuildTarget()
    {
        $this->buildRegistry->getTargets('Target 1')->willReturn(array(
            $this->target1->reveal(),
            $this->target2->reveal()
        ));

        $this->target2->setContainer($this->container)->shouldBeCalled();
        $this->target1->setContext(Argument::type('Massive\Bundle\BuildBundle\Build\TargetContext'))
            ->shouldBeCalled();
        $this->target2->setContext(Argument::type('Massive\Bundle\BuildBundle\Build\TargetContext'))
            ->shouldBeCalled();

        $this->target1->build()->shouldBeCalled();
        $this->target2->build()->shouldBeCalled();

        $res = $this->execute(array('target' => 'Target 1'), array());
        $this->assertEquals(0, $res);
    }

    public function testBuildNotTarget()
    {
        $this->buildRegistry->getTargets(null)->willReturn(array(
            $this->target1->reveal(),
            $this->target2->reveal()
        ));

        $this->execute(array(), array());
        $display = $this->tester->getDisplay();
        $this->assertContains('Target 1', $display);
        $this->assertContains('Target 2', $display);
    }

    protected function execute(array $input, $options)
    {
        return $this->tester->execute(array_merge(array(
            '--verbose' => true,
            '--no-interaction' => true
        ), $input), $options);
    }

    protected function setupTarget($target, $title, $deps)
    {
        $target->getName()->willReturn($title);
        $target->getDeps()->willReturn($deps);
    }
}

interface TestContainerAwareTarget extends BuilderInterface, ContainerAwareInterface
{
}
