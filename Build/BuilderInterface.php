<?php

namespace MassiveArt\Bundle\BuildBundle\Build;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface BuilderInterface
{
    public function getName();

    public function getDependencies();

    public function build(InputInterface $input, OutputInterface $output);
}
