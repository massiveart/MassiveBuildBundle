<?php

namespace Massive\Bundle\BuildBundle\Tests\Build;

use Prophecy\PhpUnit\ProphecyTestCase;
use Massive\Bundle\BuildBundle\Build\BuilderContext;

class BuilderContextTest extends ProphecyTestCase
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
