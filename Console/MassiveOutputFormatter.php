<?php

namespace Massive\Bundle\BuildBundle\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * This class enables the console output to be indented
 */
class MassiveOutputFormatter extends OutputFormatter
{
    /**
     * @var integer
     */
    private $indentLevel = 0;

    /**
     * Number of spaces to indent output with.
     *
     * @param integer
     */
    public function setIndentLevel($level)
    {
        $this->indentLevel = $level;
    }

    /**
     * {@inheritDoc}
     */
    public function format($message)
    {
        $out = parent::format($message);
        $lines = explode("\n", $out);
        foreach ($lines as &$line) {
            $line = str_repeat('    ', $this->indentLevel) . $line;
        }

        return implode("\n", $lines);
    }
}
