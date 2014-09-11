<?php

namespace Massive\Bundle\BuildBundle\Build;

class BuildRegistry
{
    protected $builders = array();

    public function addBuilder(BuilderInterface $builder)
    {
        if (isset($this->builders[$builder->getName()])) {
            throw new \RuntimeException(sprintf(
                'A builder with name "%s" has already been added', $builder->getName()
            ));
        }

        $this->builders[$builder->getName()] = $builder;
    }

    public function getBuilder($name)
    {
        if (!isset($this->builders[$name])) {
            throw new \RuntimeException(sprintf(
                'Trying to get non-existent builder "%s"', $name
            ));
        }

        return $this->builders[$name];
    }

    public function getBuilders($target = null)
    {
        $builders = $this->builders;

        if (null !== $target) {
            $builders = $this->getBuildersForTarget($target);
        }
        $orderedBuilders = $this->resolveDependencies($builders);

        return $orderedBuilders;
    }

    protected function getBuildersForTarget($target, &$list = array(), &$resolved = array())
    {
        $builders = array();
        $builder = $this->builders[$target];
        $dependencies = $builder->getDependencies();

        foreach ($dependencies as $dependency) {
            $list[$dependency] = $this->builders[$dependency];
            if (!isset($resolved[$dependency])) {
                $this->getBuildersForTarget($dependency, $list, $resolved);
            }
        }

        $list[$target] = $builder;
        $resolved[$target] = true;

        return array_values($list);
    }

    /**
     * Algorithim heavily influenced by:
     * https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/Loader.php
     */
    protected function resolveDependencies($builders)
    {
        $builderSequence = array();

        foreach ($builders as $builder) {
            $dependencies = $builder->getDependencies();

            if (!$dependencies) {
                $builderSequence[$builder->getName()] = 0;
                continue;
            }

            foreach ($dependencies as $dependency) {
                if (!isset($this->builders[$dependency])) {
                    throw new \RuntimeException(sprintf(
                        'Builder "%s" has dependency on unknown builder "%s"',
                        $builder->getName(), $dependency
                    ));

                }
            }

            $builderSequence[$builder->getName()] = -1;
        }

        $sequence = 1;
        $lastCount = -1;

        while (($count = count($unsequencedBuilders = $this->getUnsequencedBuilders($builderSequence))) > 0 && $count !== $lastCount) {
            foreach ($unsequencedBuilders as $key => $builderName) {
                $builder = $this->builders[$builderName];
                $dependencies = $builder->getDependencies();
                $unsequencedDependencies = $this->getUnsequencedBuilders($builderSequence, $dependencies);

                if (count($unsequencedDependencies) === 0) {
                    $builderSequence[$builderName] = $sequence++;
                }
            }

            $lastCount = $count;
        }

        if ($count > 0) {
            throw new \RuntimeException(sprintf(
                'There is a circular refernece in the builder chain.'
            ));
        }

        asort($builderSequence);
        $orderedBuilders = array();

        foreach (array_keys($builderSequence) as $builderName) {
            $orderedBuilders[] = $this->builders[$builderName];
        }

        return $orderedBuilders;
    }

    protected function getUnsequencedBuilders($sequences, $dependencies = null)
    {
        $unsequencedBuilders = array();

        if (null === $dependencies) {
            $dependencies = array_keys($sequences);
        }

        foreach ($dependencies  as $dependency) {
            if ($sequences[$dependency] === -1) {
                $unsequencedBuilders[] = $dependency;
            }
        }

        return $unsequencedBuilders;
    }
}
