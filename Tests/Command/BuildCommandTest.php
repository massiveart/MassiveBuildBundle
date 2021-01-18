<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Tests\Command;

use Massive\Bundle\BuildBundle\Build\BuilderInterface;
use Massive\Bundle\BuildBundle\Command\BuildCommand;
use Prophecy\Argument;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class BuildCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->buildRegistry = $this->prophesize('Massive\Bundle\BuildBundle\Build\BuildRegistry');
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->builder1 = $this->prophesize('Massive\Bundle\BuildBundle\Build\BuilderInterface');
        $this->builder2 = $this->prophesize('Massive\Bundle\BuildBundle\Tests\Command\TestContainerAwareBuilder');

        $this->setupBuilder($this->builder1, 'Builder 1', ['Builder 2']);
        $this->setupBuilder($this->builder2, 'Builder 2', []);

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
        return $this->tester->execute(\array_merge([
            '--verbose' => true,
            '--no-interaction' => true,
        ], $input), $options);
    }

    protected function setupBuilder($builder, $title, $dependencies)
    {
        $builder->getName()->willReturn($title);
        $builder->getDependencies()->willReturn($dependencies);
    }

    public function testBuildNoTargetNoBuilders()
    {
        $this->buildRegistry->getBuilders(null)->willReturn([]);
        $res = $this->execute([], []);
        $this->assertEquals(0, $res);
    }

    public function testBuildTarget()
    {
        $this->buildRegistry->getBuilders('Builder 1')->willReturn([
            $this->builder1->reveal(),
            $this->builder2->reveal(),
        ]);

        $this->builder2->setContainer($this->container)->shouldBeCalled();
        $this->builder1->setContext(Argument::type('Massive\Bundle\BuildBundle\Build\BuilderContext'))
            ->shouldBeCalled();
        $this->builder2->setContext(Argument::type('Massive\Bundle\BuildBundle\Build\BuilderContext'))
            ->shouldBeCalled();

        $this->builder1->build()->shouldBeCalled();
        $this->builder2->build()->shouldBeCalled();

        $res = $this->execute(['target' => 'Builder 1'], []);
        $this->assertEquals(0, $res);
    }

    public function testBuildNotTarget()
    {
        $this->buildRegistry->getBuilders(null)->willReturn([
            $this->builder1->reveal(),
            $this->builder2->reveal(),
        ]);

        $this->execute([], []);
        $display = $this->tester->getDisplay();
        $this->assertContains('Builder 1', $display);
        $this->assertContains('Builder 2', $display);
    }
}

interface TestContainerAwareBuilder extends BuilderInterface, ContainerAwareInterface
{
}
