<?php

namespace PHPCensor\Helper;

/**
 * Provides some basic diff processing functionality.
 */
class Diff
{
    /**
     * Take a diff
     * @param string $diff
     * @return array
     */
    public function getLinePositions($diff)
    {
        if (empty($diff)) {
            return null;
        }

        $rtn = [];

        $diffLines = explode(PHP_EOL, $diff);

        while (count($diffLines)) {
            $line = array_shift($diffLines);

            if (substr($line, 0, 2) == '@@') {
                array_unshift($diffLines, $line);
                break;
            }
        }

        $lineNumber = 0;
        $position = 0;

        foreach ($diffLines as $diffLine) {
            if (preg_match('/@@\s+\-[0-9]+\,[0-9]+\s+\+([0-9]+)\,([0-9]+)/', $diffLine, $matches)) {
                $lineNumber = (int)$matches[1] - 1;
            }

            $rtn[$lineNumber] = $position;

            $lineNumber++;
            $position++;
        }

        return $rtn;
    }
}
