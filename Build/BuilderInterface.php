<?php

namespace MassiveArt\Bundle\BuildBundle\Build;

interface BuilderInterface
{
    public function getName();

    public function getDependencies();
}
