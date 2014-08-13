<?php

namespace Massive\Bundle\BuildBundle\Build;

/**
 * Interface for builder classes
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface BuilderInterface
{
    /**
     * Return the name for this builder
     *
     * @return string
     */
    public function getName();

    /**
     * Return the dependencies for this builder
     *
     * @return array
     */
    public function getDependencies();

    /**
     * Execute the build logic
     */
    public function build();

    /**
     * Set the builder context
     *
     * @param \Massive\Bundle\BuildBundle\Build\BuilderContext
     */
    public function setContext(BuilderContext $context);
}
