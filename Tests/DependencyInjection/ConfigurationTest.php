<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Tests\DependencyInjection;

use Massive\Bundle\BuildBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfiguration()
    {
        $this->assertProcessedConfigurationEquals([
            [
                'command_class' => 'Foo\Bar',
                'targets' => [
                    'all' => [
                        'dependencies' => [
                            'foo' => [],
                            'bar' => [],
                            'baz' => [],
                        ],
                    ],
                ],
            ],
        ], [
                'command_class' => 'Foo\Bar',
                'targets' => [
                    'all' => [
                        'dependencies' => [
                            'foo' => [],
                            'bar' => [],
                            'baz' => [],
                        ],
                    ],
                ],
            ]
        );
    }

    public function testTargetsAsNull()
    {
        $this->assertProcessedConfigurationEquals([
            [
                'targets' => [
                    'all' => [
                        'dependencies' => [
                            'foo' => null,
                            'bar' => null,
                            'baz' => null,
                        ],
                    ],
                ],
            ],
        ], [
                'command_class' => 'Massive\Bundle\BuildBundle\Command\BuildCommand',
                'targets' => [
                    'all' => [
                        'dependencies' => [
                            'foo' => [],
                            'bar' => [],
                            'baz' => [],
                        ],
                    ],
                ],
            ]
        );
    }
}
