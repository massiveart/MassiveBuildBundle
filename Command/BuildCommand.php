<?php

namespace Massive\Bundle\BuildBundle\Command;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Massive\Bundle\BuildBundle\Build\BuildRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Massive\Bundle\BuildBundle\Build\BuilderContext;
use Massive\Bundle\BuildBundle\Console\MassiveOutputFormatter;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\TableHelper;

class BuildCommand extends Command
{
    protected $buildRegistry;
    protected $container;
    protected $output;
    protected $input;
    protected $dialog;

    /**
     * @param BuildRegistry      $buildRegistry
     * @param ContainerInterface $container
     */
    public function __construct(BuildRegistry $buildRegistry, ContainerInterface $container)
    {
        parent::__construct();
        $this->buildRegistry = $buildRegistry;
        $this->container = $container;
        $this->dialog = new DialogHelper();
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('massive:build');
        $this->setDescription('Execute build or build targets');
        $this->setHelp(<<<EOT
Build a target:

    <info>$ php app/console %command.full_name% mytarget</info>

If you want to see which targets are available run the command without any arguments:

    <info>$ php app/console %command.full_name%</info>

By default the build system will build any dependencies that the target
has, to disable this use the <comment>--nodeps</comment> option.

    <info>$ php app/console %command.full_name% --nodeps</info>
EOT
        );

        $this->addArgument('target', InputArgument::OPTIONAL, 'Target to build', null);
        $this->addOption('nodeps', 'D', InputOption::VALUE_NONE, 'Ignore dependencies');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->output->setFormatter(new MassiveOutputFormatter(true));

        $target = $input->getArgument('target');

        if ($input->getOption('nodeps')) {
            $targets = array($this->buildRegistry->getTarget($target));
        } elseif (null === $target) {
            $targets = $this->buildRegistry->getAllTargets();
        } else {
            $targets = $this->buildRegistry->getTargets($target);
        }

        $start = microtime(true);

        $this->renderTargets($targets);

        if ($target === null) {
            return 0;
        }

        if (false === $this->input->getOption('no-interaction')) {
            $res = $this->dialog->askConfirmation($this->output, '<question>Look good? (y)</question>', true);

            if (false === $res) {
                $this->output->writeln('Bye!');

                return 0;
            }
        }

        $this->output->writeln('');
        // $this->runBuilders($targets);

        $end = microtime(true);

        $this->output->writeln(sprintf('<info>Done (%ss)</info>', number_format($end - $start, 2)));
    }

    /**
     * Render the target list
     *
     * @param BuilderInterface[] $targets
     */
    protected function renderTargets($targets)
    {
        $this->writeTitle('Build Targets');

        $table = new TableHelper();
        $table->setHeaders(array('#', 'Builder', 'Deps'));

        foreach ($targets as $i => $target) {
            $table->addRow(array(
                $i,
                $target->getName(),
                implode(', ', $target->getDeps()
            )));
        }

        $table->render($this->output);

        $this->output->writeln('');
        $this->output->writeln('<info>Options:</info>');
        $this->output->writeln('');
        foreach ($this->input->getOptions() as $optionName => $optionValue) {
            $this->output->writeln(sprintf('  - <info>%s</info>: %s', $optionName, var_export($optionValue, true)));
        }

        $this->output->writeln('');

    }

    /**
     * Execute the targets
     *
     * @param BuilderInterface[] $targets
     */
    protected function runBuilders($targets)
    {
        $this->writeTitle('Executing targets');

        $targetContext = new BuilderContext($this->input, $this->output, $this->getApplication());

        foreach ($targets as $target) {
            $this->output->getFormatter()->setIndentLevel(0);

            if ($target instanceof ContainerAwareInterface) {
                $target->setContainer($this->container);
            }

            $target->setContext($targetContext);

            $this->output->writeln(sprintf(
                '<info>Target: </info>%s', $target->getName()
            ));
            $this->output->writeln('');

            $this->output->getFormatter()->setIndentLevel(1);
            $target->build();
        }
    }

    protected function writeTitle($title)
    {
        $this->output->writeln(sprintf('<info>%s</info>', $title));
        $this->output->writeln(sprintf('<info>%s</info>', str_repeat('=', strlen($title))));
        $this->output->writeln('');
    }
}
