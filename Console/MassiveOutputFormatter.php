<?php

/*
 * This file is part of the MassiveBuildBundle
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Massive\Bundle\BuildBundle\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * This class enables the console output to be indented.
 */
class MassiveOutputFormatter extends OutputFormatter
{
    /**
     * @var int
     */
    private $indentLevel = 0;

    /**
     * Number of spaces to indent output with.
     *
     * @param int
     */
    public function setIndentLevel($level)
    {
        $this->indentLevel = $level;
    }

    public function format($message)
    {
        $out = parent::format($message);
        if (!$this->isDecorated()) {
            return $out;
        }

        $lines = \explode("\n", $out);
        foreach ($lines as &$line) {
            $line = \str_repeat('    ', $this->indentLevel) . $line;
        }

        return \implode("\n", $lines);
    }
}
