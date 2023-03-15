<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Command;

use Massive\Bundle\BuildBundle\Build\BuilderContext;
use Massive\Bundle\BuildBundle\Build\BuilderInterface;
use Massive\Bundle\BuildBundle\Build\BuildRegistry;
use Massive\Bundle\BuildBundle\Console\MassiveOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BuildCommand extends Command
{
    /**
     * @var BuildRegistry
     */
    protected $buildRegistry;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var QuestionHelper
     */
    protected $question;

    public function __construct(BuildRegistry $buildRegistry, ContainerInterface $container)
    {
        parent::__construct();
        $this->buildRegistry = $buildRegistry;
        $this->container = $container;
        $this->question = new QuestionHelper();
    }

    public function configure()
    {
        $this->setName('massive:build');
        $this->setDescription('Execute build or build targets');
        $this->setHelp(<<<EOT
Build a target:

    <info>$ php %command.full_name% mytarget</info>

If you want to see which targets are available run the command without any arguments:

    <info>$ php %command.full_name%</info>

By default the build system will build any dependencies that the target
has, to disable this use the <comment>--nodeps</comment> option.

    <info>$ php %command.full_name% --nodeps</info>
EOT
        );

        $this->addArgument('target', InputArgument::OPTIONAL, 'Target to build', null);
        $this->addOption('nodeps', 'D', InputOption::VALUE_NONE, 'Ignore dependencies');
        $this->addOption('keep-exit-code', '-k', InputOption::VALUE_NONE, 'Keep the exit code of a job if it fails');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->output->setFormatter(new MassiveOutputFormatter(true));

        $target = $input->getArgument('target');

        if ($input->getOption('nodeps')) {
            $builders = [$this->buildRegistry->getBuilder($target)];
        } else {
            $builders = $this->buildRegistry->getBuilders($target);
        }

        $start = \microtime(true);

        $this->renderTargets($builders);

        if (null === $target) {
            return 0;
        }

        if (false === $this->input->getOption('no-interaction')) {
            $question = new ConfirmationQuestion('<question>Look good? (y)</question>', true);
            if (!$this->question->ask($input, $output, $question)) {
                $this->output->writeln('Bye!');

                return 0;
            }
        }

        $this->output->writeln('');
        $exitCode = $this->runBuilders($builders);

        $end = \microtime(true);

        $this->output->writeln(\sprintf('<info>Done (%ss)</info>', \number_format($end - $start, 2)));

        if (0 !== $exitCode && $input->getOption('keep-exit-code')) {
            return $exitCode;
        }

        return 0;
    }

    /**
     * Render the target list.
     *
     * @param BuilderInterface[] $builders
     */
    protected function renderTargets($builders)
    {
        $this->writeTitle('Build Targets');

        $table = new Table($this->output);
        $table->setHeaders(['#', 'Builder', 'Deps']);

        foreach ($builders as $i => $builder) {
            $table->addRow([
                $i,
                $builder->getName(),
                \implode(', ', $builder->getDependencies()),
            ]);
        }

        $table->render();

        $this->output->writeln('');
        $this->output->writeln('<info>Options:</info>');
        $this->output->writeln('');
        foreach ($this->input->getOptions() as $optionName => $optionValue) {
            $this->output->writeln(\sprintf('  - <info>%s</info>: %s', $optionName, \var_export($optionValue, true)));
        }

        $this->output->writeln('');
    }

    /**
     * Execute the builders.
     *
     * @param BuilderInterface[] $builders
     */
    protected function runBuilders($builders)
    {
        $this->writeTitle('Executing builders');

        $builderContext = new BuilderContext($this->input, $this->output, $this->getApplication());

        $combinedExitCode = 0;
        foreach ($builders as $builder) {
            $this->output->getFormatter()->setIndentLevel(0);

            if ($builder instanceof ContainerAwareInterface) {
                $builder->setContainer($this->container);
            }

            $builder->setContext($builderContext);

            $this->output->writeln(\sprintf(
                '<info>Target: </info>%s', $builder->getName()
            ));
            $this->output->writeln('');

            $this->output->getFormatter()->setIndentLevel(1);
            $exitCode = $builder->build();
            if (null !== $exitCode && 0 !== $exitCode) {
                $combinedExitCode = $exitCode;
            }
        }

        return $combinedExitCode;
    }

    protected function writeTitle($title)
    {
        $this->output->writeln(\sprintf('<info>%s</info>', $title));
        $this->output->writeln(\sprintf('<info>%s</info>', \str_repeat('=', \strlen($title))));
        $this->output->writeln('');
    }
}
