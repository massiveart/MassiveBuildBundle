<?php

namespace Massive\Bundle\BuildBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface as SymfonyContainerAwareInterface;

if (\class_exists(SymfonyContainerAwareInterface::class)) {
    \class_alias(ContainerAwareInterface::class, SymfonyContainerAwareInterface::class);
} else {
      interface ContainerAwareInterface {
          /**
           * @return void
           */
          public function setContainer(?ContainerInterface $container = null);
      }
}
