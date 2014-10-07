<?php

namespace Massive\Bundle\BuildBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Massive\Bundle\BuildBundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfiguration()
    {
        $this->assertProcessedConfigurationEquals(array(
            array(
                'command_class' => 'Foo\Bar',
                'targets' => array(
                    'all' => array(
                        'dependencies' => array('foo', 'bar', 'baz')
                    ),
                ),
            ),
        ), array(
                'command_class' => 'Foo\Bar',
                'targets' => array(
                    'all' => array(
                        'dependencies' => array('foo', 'bar', 'baz')
                    ),
                ),
            )
        );
    }

    public function testTargetsAsNull()
    {
        $this->assertProcessedConfigurationEquals(array(
            array(
                'targets' => array(
                    'all' => array(
                        'dependencies' => array('foo', 'bar', 'baz')
                    ),
                ),
            ),
        ), array(
                'command_class' => 'Massive\Bundle\BuildBundle\Command\BuildCommand',
                'targets' => array(
                    'all' => array(
                        'dependencies' => array('foo', 'bar', 'baz')
                    ),
                ),
            )
        );
    }
}
