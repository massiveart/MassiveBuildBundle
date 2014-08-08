<?php

namespace MassiveArt\Bundle\BuildBundle\Command;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use MassiveArt\Bundle\BuildBundle\Build\BuildRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

class BuildCommand extends Command
{
    protected $buildRegistry;
    protected $container;
    protected $formatter;

    public function __construct(BuildRegistry $buildRegistry, ContainerInterface $container)
    {
        parent::__construct();
        $this->buildRegistry = $buildRegistry;
        $this->container = $container;
        $this->formatter = new FormatterHelper();
    }

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
                $builder->setContainer($this->container);
            }

            $output->writeln($this->formatter->formatBlock(array(
                '',
                'Target: ' . $builder->getName(),
                ''
            ), 'info'));

            $builder->build($input, $output);
        }

        $end = microtime(true);

        $output->writeln(sprintf('Done (%s)', number_format($end - $start, 2)));
    }
}
