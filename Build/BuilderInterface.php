<?php

namespace Massive\Bundle\BuildBundle\Build;

interface BuilderInterface
{
    public function getName();

    public function getDependencies();

    public function build();

    public function setContext(BuilderContext $context);
}
