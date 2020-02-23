<?php

namespace Massive\Bundle\BuildBundle\Tests\DependencyInjection;

use Massive\Bundle\BuildBundle\DependencyInjection\Configuration;
use Massive\Bundle\BuildBundle\Tests\BaseTestCase;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class ConfigurationTest extends BaseTestCase
{
    use ConfigurationTestCaseTrait;

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
                        'dependencies' => array(
                            'foo' => array(),
                            'bar' => array(),
                            'baz' => array(),
                        ),
                    ),
                ),
            ),
        ), array(
                'command_class' => 'Foo\Bar',
                'targets' => array(
                    'all' => array(
                        'dependencies' => array(
                            'foo' => array(),
                            'bar' => array(),
                            'baz' => array(),
                        )
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
                        'dependencies' => array(
                            'foo' => null,
                            'bar' => null,
                            'baz' => null,
                        ),
                    ),
                ),
            ),
        ), array(
                'command_class' => 'Massive\Bundle\BuildBundle\Command\BuildCommand',
                'targets' => array(
                    'all' => array(
                        'dependencies' => array(
                            'foo' => array(),
                            'bar' => array(),
                            'baz' => array(),
                        )
                    ),
                ),
            )
        );
    }
}
