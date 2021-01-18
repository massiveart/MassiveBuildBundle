<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Builder;

use Massive\Bundle\BuildBundle\Tests\BaseTestCase;

class VirtualBuilderTest extends BaseTestCase
{
    protected $builder;

    public function setUp()
    {
        $this->builder = new VirtualBuilder('builder', ['one', 'two', 'three']);
    }

    public function testBuilder()
    {
        $this->assertEquals('builder', $this->builder->getName());
        $this->assertEquals(['one', 'two', 'three'], $this->builder->getDependencies());
    }
}
