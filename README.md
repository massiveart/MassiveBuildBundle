Massive Build Bundle
====================

[![](https://travis-ci.org/massiveart/MassiveBuildBundle.png)](https://travis-ci.org/massiveart/MassiveBuildBundle)

The Massive Build Bundle provides a `massive:build` command which runs build
targets.

Targets are effectively classes which execute arbitary code, and are
registred in the dependency injection container via. tags. Targets can
depend on other targets.

The aim of this bundle is to provide an extensible, decoupled, way of building
software project environments, especially in a development context.

This tool is not mean to replace Make or Ant or Phing. The bundle should only
be used to execute build steps which are contained in the client application.

## Creating build classes

You create builder classes in your application / bundles. Each build class
should have a specific responsiblity.

A minimal example:

````php
<?php

namespace My\Web\Application;

use Massive\Bundle\BuildBundle\Build\BuilderInterface;

class MyBuilder implements BuilderInterface
{
    protected $context;

    public function getName()
    {
        return 'mybuildername';
    }

    public function getDependencies()
    {
        return array();
    }

    public function build()
    {
        $application = $this->context->getApplication();
        $input = $this->context->getInput();
        $output = $this->context->getOutput();

        $output->writeln('Hello World!');
    }

    public function setContext(BuilderContext $context)
    {
        $this->context = $context;
    }
}
````

- **getName**: Return a name for the builder.
- **getDependencies**: Return the *names* of any builders that this builder
  depends on.
- **build**: Execute all logic in this method
- **setContext**: Called automatically by the master build command, contains
  the `Input`, `Output` and `Application`.

## Registring the builder class

You can register the builder class in the dependency injection container and#
then tag it with 

````xml
<service id="sulu.core.build.builder.database" class="My\Web\Application\Builder\FooBuilder">
    <tag name="massive_build.builder" />
</service>
````
## Accessing the container

You can access the service container by implementing the
`ContainerAwareInterface`:

````php
<?php
// ...

class MyBuilder implements ContainerAwareInterface, BuilderInterface
{
    // ...
}
````

## Adding global options and customization

You can make options available to your commands (e.g. to specify that they
should `destroy` the database, or that the name of user "x" should be set to
"y").

By taking this approach you are free to customize the `build` command in
anyway that you like.

To so this you need to extend the massive `BuildCommand` and configuring the
MassiveBuild bundle to use your extended class.

````php
<?php

namespace Sulu\Bundle\CoreBundle\Command;

use Massive\Bundle\BuildBundle\Command\BuildCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SuluBuildCommand extends BuildCommand
{
    public function configure()
    {
        parent::configure();

        // change the name of the command from "massive:build"
        $this->setName('myapp:build');

        // add an option
        $this->addOption('destroy', null, InputOption::VALUE_NONE, 'Destroy existing data');
    }
}
````

## Requirements

* Symfony: 2.4+
