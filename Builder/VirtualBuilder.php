<?php

namespace Massive\Bundle\BuildBundle\Builder;

use Massive\Bundle\BuildBundle\Build\BuilderInterface;
use Massive\Bundle\BuildBundle\Build\BuilderContext;

/**
 * A builder which does nothing itself but declares
 * dependencies on other builders.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class VirtualBuilder implements BuilderInterface
{
    protected $name;
    protected $dependencies;

    public function __construct($name, $dependencies)
    {
        $this->name = $name;
        $this->dependencies = $dependencies;
    }

    /**
     * Return the name for this builder
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the dependencies for this builder
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Execute the build logic
     */
    public function build()
    {
    }

    /**
     * Set the builder context
     *
     * @param \Massive\Bundle\BuildBundle\Build\BuilderContext
     */
    public function setContext(BuilderContext $context)
    {
    }
}
