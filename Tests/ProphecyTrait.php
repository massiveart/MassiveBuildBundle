<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Tests;

use Prophecy\PhpUnit\ProphecyTrait as ProphecyProphecyTrait;

if (\trait_exists(ProphecyProphecyTrait::class)) {
    \class_alias(ProphecyProphecyTrait::class, ProphecyTrait::class);
} else {
    trait ProphecyTrait
    {
        // provided by phpunit 8
    }
}
