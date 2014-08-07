<?php

namespace MassiveArt\Bundle\BuildBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

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

        if (null === $target) {
            return $this->explain();
        }

        $builder = $this->buildRegistry->getBuilders($target);

        foreach ($builders as $builder) {
            if ($builder instanceof ContainerAwareInterface) {
                $builder->setContainer($this->getContainer());
            }

            $builder->build($input, $output);
        }
    }
}
