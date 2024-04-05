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

use Massive\Bundle\BuildBundle\DependencyInjection\Compiler\BuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @final
 */
class MassiveBuildBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BuilderPass());
    }
}
