<?php

namespace Massive\Bundle\BuildBundle\Builder;

class VirtualBuilderTest extends \PHPUnit_Framework_TestCase
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
