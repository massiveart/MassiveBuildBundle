<?php

namespace MassiveArt\Bundle\BuildBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class BuildCommand extends ContainerAwareCommand
{
    protected $buildRegistry;

    public function configure()
    {
        $this->setName('massive:build');
        $this->addArgument('target', InputArgument::OPTIONAL, 'Build the specified target', null);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getArgument('target');
        $builders = $this->buildRegistry->getBuilders($target);

        $start = microtime(true);

        foreach ($builders as $builder) {
            if ($builder instanceof ContainerAwareInterface) {
                $builder->setContainer($this->getContainer());
            }

            $output->writeln($this->getDialog('formatter')->formatBlock(array(
                '',
                'Target: ' . $builder->getName(),
                ''
            )));

            $builder->build($input, $output);
        }

        $end = microtime(true);

        $output->writeln(sprintf('Done (%s)', number_format($end - $start, 2)));
    }
}
