<?php

namespace Massive\Bundle\BuildBundle\Build;

use Massive\Bundle\BuildBundle\Build\BuilderConfigContext;

/**
 * Target is something which can be executed by the user. The
 * target defines builders which will be executed in order and
 * also dependencies on other targets.
 *
 * Targets can also define options which will globally replace builder
 * options.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class Target
{
    protected $name;
    protected $builderConfigs = array();
    protected $dependencies = array();
    protected $optionReplacements = array();

    public function __construct($name, $builderConfigs, $dependencies, $optionReplacements)
    {
        $this->name = $name;
        $this->builderConfigs = $builderConfigs;
        $this->dependencies = $dependencies;
        $this->optionReplacements = $optionReplacements;
    }

    /**
     * Return the name for this builderConfig
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the builderConfig configurations
     */
    public function getBuilderConfigs() 
    {
        return $this->builderConfigs;
    }

    /**
     * Options which will replace any options declared by dependent targets
     *
     * @return array
     */
    public function getOptionReplacements() 
    {
        return $this->optionReplacements;
    }

    /**
     * Return the dependencies for this builderConfig
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
}
