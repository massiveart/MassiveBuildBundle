<?php

namespace Massive\Bundle\BuildBundle\Builder;

use Massive\Bundle\BuildBundle\Tests\BaseTestCase;

class VirtualBuilderTest extends BaseTestCase
{
    protected $builder;

    public function setUp()
    {
        $this->builder = new VirtualBuilder('builder', array('one', 'two', 'three'));
    }

    public function testBuilder()
    {
        $this->assertEquals('builder', $this->builder->getName());
        $this->assertEquals(array('one', 'two', 'three'), $this->builder->getDependencies());
    }
}
