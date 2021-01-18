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

use Massive\Bundle\BuildBundle\Build\BuilderContext;

class BuilderContextTest extends \PHPUnit_Framework_TestCase
{
    protected $input;
    protected $output;

    public function setUp()
    {
        parent::setUp();
        $this->input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $this->output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $this->application = $this->prophesize('Symfony\Component\Console\Application');

        $this->context = new BuilderContext(
            $this->input->reveal(),
            $this->output->reveal(),
            $this->application->reveal()
        );
    }

    public function testGetters()
    {
        $this->assertSame($this->input->reveal(), $this->context->getInput());
        $this->assertSame($this->output->reveal(), $this->context->getOutput());
        $this->assertSame($this->application->reveal(), $this->context->getApplication());
    }
}
