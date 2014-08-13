<?php

namespace Massive\Bundle\BuildBundle\Build;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuilderContext
{
    protected $input;
    protected $output;
    protected $application;

    public function __construct(InputInterface $input, OutputInterface $output, Application $application = null)
    {
        $this->input = $input;
        $this->output = $output;
        $this->application = $application;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getApplication()
    {
        return $this->application;
    }

}
