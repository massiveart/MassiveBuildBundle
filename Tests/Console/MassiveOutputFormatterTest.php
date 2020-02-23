<?php

namespace Massive\Bundle\BuildBundle\Tests\Console;

use Massive\Bundle\BuildBundle\Console\MassiveOutputFormatter;
use Massive\Bundle\BuildBundle\Tests\BaseTestCase;

class MassiveOutputFormatterTest extends BaseTestCase
{

    /**
     * @param string $message
     * @param int    $indent
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
     * @param int    $indent
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
