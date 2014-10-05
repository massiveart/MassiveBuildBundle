<?php

namespace Massive\Bundle\BuildBundle\Build;

use Massive\Bundle\BuildBundle\Build\Target;

class BuildRegistry
{
    protected $builders = array();
    protected $targets = array();

    /**
     * Register a builder
     *
     * @param BuilderInterface
     */
    public function addBuilder(BuilderInterface $builder)
    {
        if (isset($this->builders[$builder->getName()])) {
            throw new \RuntimeException(sprintf(
                'A builder with name "%s" has already been added', $builder->getName()
            ));
        }

        $this->builders[$builder->getName()] = $builder;
    }

    /**
     * Register a target
     *
     * @param Target
     */
    public function addTarget(Target $target)
    {
        if (isset($this->targets[$target->getName()])) {
            throw new \RuntimeException(sprintf(
                'A target with name "%s" has already been added', $target->getName()
            ));
        }

        $this->targets[$target->getName()] = $target;
    }

    /**
     * Return the named builder
     *
     * @param string
     *
     * @return BuilderInterface
     * @throws RuntimeException
     */
    public function getBuilder($name)
    {
        if (!isset($this->builders[$name])) {
            throw new \RuntimeException(sprintf(
                'Trying to get non-existent builder "%s"', $name
            ));
        }

        return $this->builders[$name];
    }

    /**
     * Return the named target
     *
     * @param string
     *
     * @return Target
     * @throws RuntimeException
     */
    public function getTarget($name)
    {
        if (!isset($this->targets[$name])) {
            throw new \RuntimeException(sprintf(
                'Trying to get non-existent target "%s"', $name
            ));
        }

        return $this->targets[$name];
    }

    /**
     * Return the given target and all of its dependencies
     * in the correct order
     *
     * @param string $targetName
     *
     * @return Target[]
     * @throws InvalidArgumentException
     */
    public function getTargets($targetName)
    {
        $target = $this->getTarget($targetName);

        $targets = $this->getTargetsForTarget($target);
        $targets = $this->resolveTargets($targets);

        return $targets;
    }

    /**
     * Return all targets
     *
     * @return Target[]
     */
    public function getAllTargets()
    {
        return $this->targets;
    }


    /**
     * Recursively seek out all the targets and return them
     * 
     * @param Target $target
     * @param array $list
     * @param array $resolved
     *
     * @return Target[]
     */
    private function getTargetsForTarget(Target $target, &$list = array(), &$resolved = array())
    {
        $targets = array();
        $targetName = $target->getName();
        $target = $this->getTarget($targetName);
        $deps = $target->getDeps();

        foreach ($deps as $dep) {
            $list[$dep] = $targetDep = $this->getTarget($dep);
            if (!isset($resolved[$dep])) {
                $this->getTargetsForTarget($targetDep, $list, $resolved);
            }
        }

        $list[$targetName] = $target;
        $resolved[$targetName] = true;

        return array_values($list);
    }

    /**
     * Algorithim heavily influenced by:
     * https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/Loader.php
     */
    private function resolveTargets($targets)
    {
        $targetSequence = array();

        foreach ($targets as $target) {
            $dependencies = $target->getDeps();

            if (!$dependencies) {
                $targetSequence[$target->getName()] = 0;
                continue;
            }

            foreach ($dependencies as $dependency) {
                if (!isset($this->targets[$dependency])) {
                    throw new \RuntimeException(sprintf(
                        'Target "%s" has dependency on unknown target "%s"',
                        $target->getName(), $dependency
                    ));
                }
            }

            $targetSequence[$target->getName()] = -1;
        }

        $sequence = 1;
        $lastCount = -1;

        while (($count = count($unsequencedTargets = $this->getUnsequencedTargets($targetSequence))) > 0 && $count !== $lastCount) {
            foreach ($unsequencedTargets as $key => $targetName) {
                $target = $this->targets[$targetName];
                $dependencies = $target->getDeps();
                $unsequencedDeps = $this->getUnsequencedTargets($targetSequence, $dependencies);

                if (count($unsequencedDeps) === 0) {
                    $targetSequence[$targetName] = $sequence++;
                }
            }

            $lastCount = $count;
        }

        if ($count > 0) {
            throw new \RuntimeException(sprintf(
                'There is a circular refernece in the target chain.'
            ));
        }

        asort($targetSequence);
        $orderedTargets = array();

        foreach (array_keys($targetSequence) as $targetName) {
            $orderedTargets[] = $this->targets[$targetName];
        }

        return $orderedTargets;
    }

    private function getUnsequencedTargets($sequences, $dependencies = null)
    {
        $unsequencedTargets = array();

        if (null === $dependencies) {
            $dependencies = array_keys($sequences);
        }

        foreach ($dependencies  as $dependency) {
            if ($sequences[$dependency] === -1) {
                $unsequencedTargets[] = $dependency;
            }
        }

        return $unsequencedTargets;
    }
}
