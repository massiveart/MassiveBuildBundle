<?php

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
