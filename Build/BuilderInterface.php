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
     * Execute the build logic
     */
    public function build($options = array());

    /**
     * Set the builder context
     *
     * @param \Massive\Bundle\BuildBundle\Build\BuilderContext
     */
    public function setContext(BuilderContext $context);
}
