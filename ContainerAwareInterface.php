<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle;

use Symfony\Component\DependencyInjection\ContainerAwareInterface as SymfonyContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

if (\class_exists(SymfonyContainerAwareInterface::class)) {
    \class_alias(ContainerAwareInterface::class, SymfonyContainerAwareInterface::class);
} else {
    interface ContainerAwareInterface
    {
        /**
         * @return void
         */
        public function setContainer(?ContainerInterface $container = null);
    }
}
