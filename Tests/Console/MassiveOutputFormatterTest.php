<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Tests\Console;

use Massive\Bundle\BuildBundle\Console\MassiveOutputFormatter;

class MassiveOutputFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $message
     * @param int $indent
     * @param string $expected
     *
     * @dataProvider getDecorationDataProvider
     */
    public function testThatOutputWillBeDecorated($message, $indent, $expected)
    {
        $formatter = new MassiveOutputFormatter(true);
        $formatter->setIndentLevel($indent);
        $result = $formatter->format($message);
        $this->assertEquals($expected, $result);
    }

    public function getDecorationDataProvider()
    {
        return [
            ['Some string', 0, 'Some string'],
            ['Some string', 1, '    Some string'],
            ['Some string', 2, '        Some string'],
        ];
    }

    /**
     * @param string $message
     * @param int $indent
     *
     * @dataProvider getNonDecorationDataProvider
     */
    public function testThatOutputWillNotBeDecorated($message, $indent)
    {
        $formatter = new MassiveOutputFormatter(false);
        $formatter->setIndentLevel($indent);
        $result = $formatter->format($message);
        $this->assertEquals($message, $result);
    }

    public function getNonDecorationDataProvider()
    {
        return [
            ['Some string', 0],
            ['Some string', 1],
            ['Some string', 2],
        ];
    }
}
